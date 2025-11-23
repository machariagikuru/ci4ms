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

        $cap = new \Gregwar\Captcha\CaptchaBuilder();
        $cap->setBackgroundColor(139, 203, 183);
        $cap->setIgnoreAllEffects(false);
        $cap->setMaxFrontLines(0);
        $cap->setMaxBehindLines(0);
        $cap->setMaxAngle(1);
        $cap->setTextColor(18, 58, 73);
        $cap->setLineColor(18, 58, 73);
        $cap->build();

        session()->setFlashdata('cap', $cap->getPhrase());
        return $this->respond(['capIMG' => $cap->inline()], 200);
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
            'captcha'     => ['Captcha' => 'Captcha', 'rules' => 'required']
        ];

        if (!$this->validate($valData)) {
            return $this->fail($this->validator->getErrors());
        }

        if ($this->request->getPost('captcha') == session()->getFlashdata('cap')) {
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
                'blog_id'    => $this->request->getPost('blog_id'),
                'created_at' => date('Y-m-d H:i:s'),
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

        return $this->fail('Please get a new captcha !');
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

    // We will move methods here in the next step
}