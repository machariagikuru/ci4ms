<?php

namespace Modules\Blog\Controllers;

use JasonGrimes\Paginator;
use Modules\Backend\Libraries\CommonTagsLibrary;
use Modules\Backend\Models\AjaxModel;

class Blog extends \Modules\Backend\Controllers\BaseController
{
    /**
     * @var CommonTagsLibrary
     */
    private $commonTagsLib;

    /**
     * @var AjaxModel
     */
    private $model;

    /**
     * Blog constructor.
     */
    public function __construct()
    {
        $this->model = new AjaxModel();
        $this->commonTagsLib = new CommonTagsLibrary();
    }

    /**
     * Display list of blogs.
     *
     * @return string
     */
    public function index()
    {
        $totalItems = $this->commonModel->count('blog', []);
        $itemsPerPage = 20;
        $currentPage = (int) $this->request->getUri()->getSegment(3, 1);
        $urlPattern = '/backend/blogs/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $paginator->setMaxPagesToShow(5);
        $offset = ($currentPage - 1) * $itemsPerPage;

        $this->defData = array_merge($this->defData, [
            'paginator' => $paginator,
            'blogs' => $this->commonModel->lists('blog', '*', [], 'id ASC', $itemsPerPage, $offset),
        ]);

        return view('Modules\Blog\Views\list', $this->defData);
    }

    /**
     * Show form to create a new blog post.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function new()
    {
        if ($this->request->is('post')) {
            $valData = [
                'title' => ['label' => lang('Backend.title'), 'rules' => 'required'],
                'seflink' => ['label' => lang('Backend.url'), 'rules' => 'required'],
                'content' => ['label' => lang('Backend.content'), 'rules' => 'required'],
                'isActive' => ['label' => lang('Backend.publish') . ' / ' . lang('Backend.draft'), 'rules' => 'required'],
                'categories' => ['label' => lang('Blog.categories'), 'rules' => 'required'],
                'author' => ['label' => lang('Blog.author'), 'rules' => 'required'],
                'created_at' => ['label' => lang('Backend.createdAt'), 'rules' => 'required|valid_date[d.m.Y H:i:s]'],
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $valData['pageimg'] = ['label' => lang('Backend.coverImage'), 'rules' => 'required'];
                $valData['pageIMGWidth'] = ['label' => lang('Backend.coverImgWith'), 'rules' => 'required|is_natural_no_zero'];
                $valData['pageIMGHeight'] = ['label' => lang('Backend.coverImgHeight'), 'rules' => 'required|is_natural_no_zero'];
            }

            if (!empty($this->request->getPost('description'))) {
                $valData['description'] = ['label' => lang('Backend.seoDescription'), 'rules' => 'required'];
            }

            if (!empty($this->request->getPost('keywords'))) {
                $valData['keywords'] = ['label' => lang('Backend.seoKeywords'), 'rules' => 'required'];
            }

            if (!$this->validate($valData)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->commonModel->isHave('blog', ['seflink' => $this->request->getPost('seflink')]) === 1) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Blog seflink adresi daha önce kullanılmış. Lütfen kontrol ederek tekrar deneyiniz.');
            }

            $data = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'isActive' => (bool) $this->request->getPost('isActive'),
                'seflink' => $this->request->getPost('seflink'),
                'inMenu' => false,
                'author' => $this->request->getPost('author'),
                'created_at' => date('Y-m-d H:i:s', strtotime($this->request->getPost('created_at'))),
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $data['seo']['coverImage'] = $this->request->getPost('pageimg');
                $data['seo']['IMGWidth'] = $this->request->getPost('pageIMGWidth');
                $data['seo']['IMGHeight'] = $this->request->getPost('pageIMGHeight');
            }

            if (!empty($this->request->getPost('description'))) {
                $data['seo']['description'] = $this->request->getPost('description');
            }

            if (!empty($data['seo'])) {
                $data['seo'] = json_encode($data['seo'], JSON_UNESCAPED_UNICODE);
            }

            $insertID = $this->commonModel->create('blog', $data);

            if ($insertID) {
                if (!empty($this->request->getPost('categories'))) {
                    foreach ($this->request->getPost('categories') as $category) {
                        $this->commonModel->create('blog_categories_pivot', [
                            'blog_id' => $insertID,
                            'categories_id' => $category,
                        ]);
                    }
                }

                if (!empty($this->request->getPost('keywords'))) {
                    $this->commonTagsLib->checkTags($this->request->getPost('keywords'), 'blogs', (string) $insertID, 'tags');
                }

                return redirect()->route('blogs', [1])
                    ->with('message', '<b>' . esc($this->request->getPost('title')) . '</b> adlı blog oluşturuldu.');
            }

            return redirect()->back()->withInput()->with('error', lang('Backend.created', [$data['title']]));
        }

        $this->defData['categories'] = $this->commonModel->lists('categories');
        $this->defData['authors'] = $this->commonModel->lists('users', '*', ['status' => 'active']);

        return view('Modules\Blog\Views\create', $this->defData);
    }

    /**
     * Edit an existing blog post.
     *
     * @param string $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit(string $id)
    {
        if ($this->request->is('post')) {
            $valData = [
                'title' => ['label' => lang('Backend.title'), 'rules' => 'required'],
                'seflink' => ['label' => lang('Backend.url'), 'rules' => 'required'],
                'content' => ['label' => lang('Backend.content'), 'rules' => 'required'],
                'isActive' => ['label' => lang('Backend.publish') . ' / ' . lang('Backend.draft'), 'rules' => 'required'],
                'categories' => ['label' => lang('Blog.categories'), 'rules' => 'required'],
                'author' => ['label' => lang('Blog.author'), 'rules' => 'required'],
                'created_at' => ['label' => lang('Backend.createdAt'), 'rules' => 'required|valid_date[d.m.Y H:i:s]'],
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $valData['pageimg'] = ['label' => lang('Backend.coverImage'), 'rules' => 'required'];
                $valData['pageIMGWidth'] = ['label' => lang('Backend.coverImgWith'), 'rules' => 'required|is_natural_no_zero'];
                $valData['pageIMGHeight'] = ['label' => lang('Backend.coverImgHeight'), 'rules' => 'required|is_natural_no_zero'];
            }

            if (!empty($this->request->getPost('description'))) {
                $valData['description'] = ['label' => lang('Backend.seoDescription'), 'rules' => 'required'];
            }

            if (!empty($this->request->getPost('keywords'))) {
                $valData['keywords'] = ['label' => lang('Backend.seoKeywords'), 'rules' => 'required'];
            }

            if (!$this->validate($valData)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $info = $this->commonModel->selectOne('blog', ['id' => $id]);
            if ($info && $info->seflink !== $this->request->getPost('seflink')) {
                if ($this->commonModel->isHave('blog', ['seflink' => $this->request->getPost('seflink')]) === 1) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Blog seflink adresi daha önce kullanılmış. Lütfen kontrol ederek tekrar deneyiniz.');
                }
            }

            $data = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'isActive' => (bool) $this->request->getPost('isActive'),
                'seflink' => $this->request->getPost('seflink'),
                'author' => $this->request->getPost('author'),
                'created_at' => date('Y-m-d H:i:s', strtotime($this->request->getPost('created_at'))),
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $data['seo']['coverImage'] = $this->request->getPost('pageimg');
                $data['seo']['IMGWidth'] = $this->request->getPost('pageIMGWidth');
                $data['seo']['IMGHeight'] = $this->request->getPost('pageIMGHeight');
            }

            if (!empty($this->request->getPost('description'))) {
                $data['seo']['description'] = $this->request->getPost('description');
            }

            if (!empty($data['seo'])) {
                $data['seo'] = json_encode($data['seo'], JSON_UNESCAPED_UNICODE);
            }

            if ($this->commonModel->edit('blog', $data, ['id' => $id])) {
                if (!empty($this->request->getPost('keywords'))) {
                    $this->commonTagsLib->checkTags($this->request->getPost('keywords'), 'blogs', $id, 'tags', true);
                }

                if (!empty($this->request->getPost('categories'))) {
                    $this->commonModel->remove('blog_categories_pivot', ['blog_id' => $id]);
                    foreach ($this->request->getPost('categories') as $category) {
                        $this->commonModel->create('blog_categories_pivot', [
                            'blog_id' => $id,
                            'categories_id' => $category,
                        ]);
                    }
                }

                return redirect()->route('blogs', [1])
                    ->with('message', lang('Backend.updated', [$data['title']]));
            }

            return redirect()->back()->withInput()->with('error', lang('Backend.notUpdated', [$data['title']]));
        }

        $tags = $this->model->limitTags_ajax(['tags_pivot.tagType' => 'blogs', 'tags_pivot.piv_id' => $id]);
        $formattedTags = [];
        foreach ($tags as $tag) {
            $formattedTags[] = ['id' => (string) $tag->id, 'value' => $tag->tag];
        }

        $this->defData['tags'] = json_encode($formattedTags, JSON_UNESCAPED_UNICODE);
        $this->defData['categories'] = $this->commonModel->lists('categories');
        $this->defData['infos'] = $info = $this->commonModel->selectOne('blog', ['id' => $id]);

        if ($info && !empty($info->seo)) {
            $this->defData['infos']->seo = json_decode($info->seo);
        }

        $this->defData['infos']->categories = $this->commonModel->lists('blog_categories_pivot', '*', ['blog_id' => $id]);
        $this->defData['authors'] = $this->commonModel->lists('users', '*', ['status' => 'active']);

        return view('Modules\Blog\Views\update', $this->defData);
    }

    /**
     * Delete a blog post.
     *
     * @param int|null $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id = null)
    {
        if ($id && $this->commonModel->remove('blog', ['id' => $id])) {
            return redirect()->route('blogs', [1])->with('message', 'Blog silindi.');
        }

        return redirect()->back()->withInput()->with('error', 'Blog silinemedi.');
    }

    /**
     * Display comment list page.
     *
     * @return string
     */
    public function commentList()
    {
        return view('Modules\Blog\Views\commentList', $this->defData);
    }

    /**
     * AJAX response for comment datatable.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function commentResponse()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden();
        }

        $data = clearFilter($this->request->getPost());
        $like = $data['search']['value'] ?? '';
        $isApproved = ($this->request->getPost('isApproved') === 'true');
        $searchData = ['isApproved' => $isApproved];
        $likeFields = !empty($like) ? ['comFullName' => $like, 'comEmail' => $like] : [];

        $results = $this->commonModel->lists(
            'comments',
            '*',
            $searchData,
            'id DESC',
            (int) $data['length'],
            (int) $data['start'],
            $likeFields
        );

        $totalRecords = $this->commonModel->count('comments', $searchData);

        $output = [];
        $counter = (int) $data['start'] + 1;

        foreach ($results as $row) {
            $output[] = [
                'id' => $counter++,
                'com_name_surname' => esc($row->comFullName),
                'email' => esc($row->comEmail),
                'created_at' => $row->created_at,
                'status' => $row->isApproved ? 'Approved' : 'Not approved',
                'process' => '<a href="' . route_to('displayComment', $row->id) . '"
                               class="btn btn-outline-info btn-sm">' . lang('Backend.update') . '</a>
                            <a href="' . route_to('commentRemove', $row->id) . '"
                               class="btn btn-outline-danger btn-sm">' . lang('Backend.delete') . '</a>',
            ];
        }

        return $this->respond([
            'draw' => (int) $data['draw'],
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData' => $output,
        ]);
    }

    /**
     * Delete a comment.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function commentRemove(int $id)
    {
        if ($this->commonModel->remove('comments', ['id' => $id])) {
            return redirect()->route('comments')->with('warning', lang('Backend.deleted', ['#' . $id]));
        }

        return redirect()->back()->withInput()->with('error', lang('Backend.notDeleted', ['#' . $id]));
    }

    /**
     * Display a single comment.
     *
     * @param int $id
     * @return string
     */
    public function displayComment(int $id)
    {
        $this->defData['commentInfo'] = $this->commonModel->selectOne('comments', ['id' => $id]);
        if ($this->defData['commentInfo']) {
            $this->defData['blogInfo'] = $this->commonModel->selectOne('blog', ['id' => $this->defData['commentInfo']->blog_id]);
        }

        return view('Modules\Blog\Views\displayComment', $this->defData);
    }

    /**
     * Approve or delete a comment based on user choice.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function confirmComment(int $id)
    {
        $rules = [
            'options' => 'required|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isApproved = (int) $this->request->getPost('options');

        if ($isApproved === 1) {
            if ($this->commonModel->edit('comments', ['isApproved' => true], ['id' => $id])) {
                return redirect()->route('comments')->with('message', lang('Blog.commentPublished', [$id]));
            }

            return redirect()->back()->withInput()->with('error', lang('Blog.commentPublishError'));
        }

        // Option 2: Delete comment
        if ($this->commonModel->remove('comments', ['id' => $id])) {
            return redirect()->route('comments')->with('warning', lang('Backend.deleted', ['#' . $id]));
        }

        return redirect()->back()->withInput()->with('error', lang('Backend.notDeleted', ['#' . $id]));
    }

    /**
     * Show bad words list.
     *
     * @return string
     */
    public function badwordList()
    {
        $setting = $this->commonModel->selectOne('settings', ['option' => 'badwords'], 'content');
        $badwords = $setting ? json_decode($setting->content, true, 512, JSON_UNESCAPED_UNICODE) : null;

        if (empty($badwords)) {
            $this->defData['badwords'] = null;
        } else {
            $this->defData['badwords'] = (object) [
                'list' => implode(',', $badwords['list']),
                'status' => $badwords['status'],
                'autoReject' => $badwords['autoReject'],
                'autoAccept' => $badwords['autoAccept'],
            ];
        }

        return view('Modules\Blog\Views\badwordlist', $this->defData);
    }

    /**
     * Save bad words settings.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function badwordsAdd()
    {
        $data = [
            'status' => $this->request->getPost('status') === 'on' ? 1 : 0,
            'autoReject' => $this->request->getPost('autoReject') === 'on' ? 1 : 0,
            'autoAccept' => $this->request->getPost('autoAccept') === 'on' ? 1 : 0,
            'list' => explode(',', $this->request->getPost('badwords')),
        ];

        if ($this->commonModel->edit('settings', [
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ], ['option' => 'badwords'])) {
            return redirect()->route('badwords')->with('message', lang('Backend.updated'));
        }

        return redirect()->back()->withInput()->with('error', lang('Backend.notUpdated'));
    }
}