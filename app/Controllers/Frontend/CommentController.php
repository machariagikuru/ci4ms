<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Libraries\CommonLibrary;
use App\Models\Ci4ms;
use Modules\Backend\Models\AjaxModel;

class CommentController extends BaseController
{
    private CommonLibrary $commonLibrary;
    private Ci4ms $ci4msModel;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        $this->ci4msModel = new Ci4ms();
        // Note: $this->defData, $this->commonModel, etc. are inherited from BaseController
    }

    public function commentCaptcha()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden();
        }

        // Generate two random numbers between 1 and 20
        $num1 = random_int(1, 20);
        $num2 = random_int(1, 20);
        $answer = $num1 + $num2;

        // Store the correct answer in the session (not flashdata)
        session()->set('comment_captcha_answer', $answer);

        // Return the question as a string
        $question = "$num1 + $num2 = ?";
        return $this->respond(['capQuestion' => $question], 200);
    }

    public function newComment()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden();
        }

        $valData = [
            'comFullName' => ['label' => 'Full name', 'rules' => 'required'],
            'comEmail'    => ['label' => 'E-mail', 'rules' => 'required|valid_email'],
            'comMessage'  => ['label' => 'Join the discussion and leave a comment!', 'rules' => 'required'],
            'captcha'     => ['label' => 'Captcha', 'rules' => 'required']
        ];

        if (!$this->validate($valData)) {
            return $this->fail($this->validator->getErrors());
        }

        $userAnswer = $this->request->getPost('captcha');
        $correctAnswer = session()->get('comment_captcha_answer');

        // Clear CAPTCHA from session after use (prevent replay)
        session()->remove('comment_captcha_answer');

        if ($userAnswer !== null && (int)$userAnswer === (int)$correctAnswer) {
            $badwordFilterSettings = json_decode($this->commonModel->selectOne(
                'settings',
                ['option' => 'badwords'],
                'content'
            )->content);

            $checked = $this->commonLibrary->commentBadwordFiltering(
                $this->request->getPost('comMessage'),
                $badwordFilterSettings->list ?? [],
                (bool)($badwordFilterSettings->status ?? false),
                (bool)($badwordFilterSettings->autoReject ?? false)
            );

            if (is_bool($checked) && !$checked) {
                return $this->fail('Lütfen kelimelerinize dikkat ediniz.');
            }

            $data = [
                'blog_id'     => $this->request->getPost('blog_id'),
                'created_at'  => date('Y-m-d H:i:s'),
                'comFullName' => $this->request->getPost('comFullName'),
                'comEmail'    => $this->request->getPost('comEmail'),
                'comMessage'  => $checked
            ];

            if (!empty($this->request->getPost('commentID'))) {
                $data['parent_id'] = $this->request->getPost('commentID');
                $this->commonModel->edit(
                    'comments',
                    ['isThereAnReply' => true],
                    ['id' => $this->request->getPost('commentID')]
                );
            }

            if ($this->commonModel->create('comments', $data)) {
                return $this->respondCreated(['result' => true]);
            }
        }

        return $this->fail('Incorrect Capcha! Click new to refresh.');
    }

    public function repliesComment()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden();
        }

        $valData = ['comID' => ['label' => 'Comment', 'rules' => 'required']];
        if (!$this->validate($valData)) {
            return $this->fail($this->validator->getErrors());
        }

        return $this->respond([
            'display' => view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/replies', [
                'replies' => $this->commonModel->lists('comments', '*', ['parent_id' => $this->request->getPost('comID')])
            ])
        ], 200);
    }

    public function loadMoreComments()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden();
        }

        $valData = [
            'blogID' => ['label' => 'Blog ID', 'rules' => 'required|string'],
            'skip'   => ['label' => 'data-skip', 'rules' => 'required|is_natural_no_zero']
        ];

        if (!empty($this->request->getPost('comID'))) {
            $valData['comID'] = ['label' => 'Comment ID', 'rules' => 'required|string'];
        }

        if (!$this->validate($valData)) {
            return $this->fail($this->validator->getErrors());
        }

        helper('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/funcs');

        $data = ['blog_id' => $this->request->getPost('blogID')];
        if (!empty($this->request->getPost('comID'))) {
            $data['parent_id'] = $this->request->getPost('comID');
        }

        $comments = $this->commonModel->lists('comments', '*', $data, 'id ASC', 5, (int)$this->request->getPost('skip'));

        return $this->respond([
            'display' => view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/loadMoreComments', [
                'comments' => $comments,
                'blogID'   => $this->request->getPost('blogID')
            ]),
            'count' => count($comments)
        ], 200);
    }
}