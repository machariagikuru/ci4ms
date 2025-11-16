<?php

namespace App\Controllers;

use App\Libraries\CommonLibrary;
use App\Models\Ci4ms;
use CodeIgniter\I18n\Time;
use Modules\Backend\Models\AjaxModel;
use Modules\Users\Models\UserscrudModel;

class Home extends BaseController
{
    private $commonLibrary;
    private $ci4msModel;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        $this->ci4msModel = new Ci4ms();
    }

    // --- Helper: Generate arithmetic CAPTCHA ---
    protected function generateMathCaptcha()
    {
        $num1 = random_int(1, 10);
        $num2 = random_int(1, 10);
        $answer = $num1 + $num2;
        // ✅ Use regular session (not flashdata) so it survives form POST
        session()->set('math_captcha_answer', $answer);
        return "$num1 + $num2";
    }

    // --- Public Pages ---
    public function index(string $seflink = '/')
    {
        $page = $this->commonModel->selectOne('pages', ['seflink' => $seflink]);
        if (!empty($page)) {
            $this->defData['pageInfo'] = $page;
            $this->defData['pageInfo']->content = $this->commonLibrary->parseInTextFunctions($this->defData['pageInfo']->content);

            $seo = !empty($page->seo) ? json_decode($page->seo, false) : (object) [];
            if (!is_object($seo)) $seo = (object) [];
            $this->defData['pageInfo']->seo = $seo;

            $keywords = [];
            if (!empty($seo->keywords) && is_array($seo->keywords)) {
                foreach ($seo->keywords as $keyword) {
                    if (isset($keyword->value)) $keywords[] = $keyword->value;
                }
            }

            $description = $seo->description ?? '';
            $coverImage = $seo->coverImage ?? '';

            $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
                $this->defData['pageInfo']->title,
                $description,
                $seflink,
                ['keywords' => $keywords],
                $coverImage
            );

            $this->defData['schema'] = $this->ci4msseoLibrary->ldPlusJson('Organization', [
                'url' => site_url(),
                'logo' => $this->defData['settings']->logo ?? '',
                'name' => $this->defData['settings']->siteName ?? '',
                'children' => [
                    'ContactPoint' => [
                        'ContactPoint' => [
                            'telephone' => $this->defData['settings']->company->phone ?? '',
                            'contactType' => 'customer support'
                        ]
                    ]
                ],
                'sameAs' => array_map(fn($sN) => $sN['link'] ?? '', (array)($this->defData['settings']->socialNetwork ?? []))
            ]);

            if ($seflink !== '/') {
                $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['pageInfo']->id, 'page');
            }

            return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/pages', $this->defData);
        }

        return show_404();
    }

    public function maintenanceMode()
    {
        if (!((bool)($this->defData['settings']->maintenanceMode->scalar ?? false))) {
            return redirect()->route('home');
        }
        return view('maintenance', $this->defData);
    }

    // --- Blog ---
    public function blog()
    {
        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags('Blog', 'blog listesi', 'blog', ['keywords' => ['blog listesi']]);

        $perPage = 12;
        $page = (int) $this->request->getUri()->getSegment(2, 1);
        $offset = ($page - 1) * $perPage;

        $this->defData['blogs'] = $this->commonModel->lists('blog', '*', ['isActive' => true], 'id ASC', $perPage, $offset);
        $totalBlogs = $this->commonModel->count('blog', ['isActive' => true]);

        $pager = \Config\Services::pager();
        $this->defData['pager'] = $pager->makeLinks($page, $perPage, $totalBlogs, $this->defData['settings']->templateInfos->path, 2);
        $this->defData['pager_info_text'] = (object) [
            'total_products' => $totalBlogs,
            'start' => ($page - 1) * $perPage + 1,
            'end' => min($page * $perPage, $totalBlogs),
        ];

        $this->defData['dateI18n'] = new Time();
        $modelTag = new AjaxModel();

        foreach ($this->defData['blogs'] as $key => $blog) {
            $this->defData['blogs'][$key]->tags = $modelTag->limitTags_ajax(['tags_pivot.piv_id' => $blog->id]);
            $this->defData['blogs'][$key]->author = $this->commonModel->selectOne('users', ['id' => $blog->author], 'firstname,sirname');
        }

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true]);

        $this->defData['schema'] = $this->ci4msseoLibrary->ldPlusJson('Organization', [
            'url' => site_url(implode('/', $this->request->getUri()->getSegments())),
            'logo' => $this->defData['settings']->logo ?? '',
            'name' => $this->defData['settings']->siteName ?? '',
            'children' => [
                'ContactPoint' => [
                    'ContactPoint' => [
                        'telephone' => $this->defData['settings']->company->phone ?? '',
                        'contactType' => 'customer support'
                    ]
                ]
            ],
            'sameAs' => array_map(fn($sN) => $sN['link'] ?? '', (array)($this->defData['settings']->socialNetwork ?? []))
        ]);

        $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs('/blog/1', 'page');
        return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/list', $this->defData);
    }

    public function blogDetail(string $seflink)
    {
        if ($this->commonModel->isHave('blog', ['seflink' => $seflink, 'isActive' => true]) !== 1) {
            return show_404();
        }

        $this->defData['infos'] = $this->commonModel->selectOne('blog', ['seflink' => $seflink]);
        $seo = !empty($this->defData['infos']->seo) ? json_decode($this->defData['infos']->seo, false) : (object) [];
        if (!is_object($seo)) $seo = (object) [];
        $this->defData['infos']->seo = $seo;

        $userModel = new UserscrudModel();
        $authorInfo = $userModel->loggedUser(0, 'users.*,auth_groups.name as groupName', ['users.id' => $this->defData['infos']->author]);
        if (empty($authorInfo)) return show_404();

        $this->defData['authorInfo'] = $authorInfo[0];
        $this->defData['dateI18n'] = new Time();
        $modelTag = new AjaxModel();
        $this->defData['tags'] = $modelTag->limitTags_ajax(['piv_id' => $this->defData['infos']->id]);

        $keywords = [];
        if (!empty($this->defData['tags'])) {
            foreach ($this->defData['tags'] as $tag) {
                $keywords[] = $tag->tag;
            }
        }

        helper('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/funcs');

        $this->defData['comments'] = $this->commonModel->lists('comments', '*', ['blog_id' => $this->defData['infos']->id], 'id ASC', 5);

        $description = $seo->description ?? '';
        $coverImage = $seo->coverImage ?? '';
        $authorName = ($this->defData['authorInfo']->firstname ?? '') . ' ' . ($this->defData['authorInfo']->sirname ?? '');

        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            $this->defData['infos']->title,
            $description,
            'blog/' . $seflink,
            ['keywords' => $keywords, 'author' => $authorName],
            $coverImage
        );

        $this->defData['categories'] = $this->commonModel->lists('categories');
        $this->defData['schema'] = $this->ci4msseoLibrary->ldPlusJson('BlogPosting', [
            'url' => site_url(implode('/', $this->request->getUri()->getSegments())),
            'logo' => $this->defData['settings']->logo ?? '',
            'name' => $this->defData['settings']->siteName ?? '',
            'headline' => $this->defData['infos']->title ?? '',
            'image' => $coverImage,
            'description' => $description,
            'datePublished' => $this->defData['infos']->created_at ?? '',
            'children' => [
                'mainEntityOfPage' => ['WebPage' => []],
                'ContactPoint' => [
                    'ContactPoint' => [
                        'telephone' => $this->defData['settings']->company->phone ?? '',
                        'contactType' => 'customer support'
                    ]
                ]
            ],
            'sameAs' => array_map(fn($sN) => $sN['link'] ?? '', (array)($this->defData['settings']->socialNetwork ?? []))
        ]);

        $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['infos']->id, 'blog');
        return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/post', $this->defData);
    }

    public function tagList(string $seflink)
    {
        if ($this->commonModel->isHave('tags', ['seflink' => $seflink]) !== 1) return show_404();

        $perPage = 12;
        $page = (int) $this->request->getUri()->getSegment(3, 1);
        $offset = ($page - 1) * $perPage;

        $this->defData['blogs'] = $this->ci4msModel->taglist(['tags.seflink' => $seflink, 'blog.isActive' => true], $perPage, $offset, 'blog.*');
        $totalBlogs = count($this->defData['blogs']);

        $pager = \Config\Services::pager();
        $this->defData['pager'] = $pager->makeLinks($page, $perPage, $totalBlogs, $this->defData['settings']->templateInfos->path, 3);
        $this->defData['pager_info_text'] = (object) [
            'total_products' => $totalBlogs,
            'start' => ($page - 1) * $perPage + 1,
            'end' => min($page * $perPage, $totalBlogs),
        ];

        $this->defData['dateI18n'] = new Time();
        $modelTag = new AjaxModel();

        foreach ($this->defData['blogs'] as $key => $blog) {
            $this->defData['blogs'][$key]->tags = $modelTag->limitTags_ajax(['piv_id' => $blog->id]);
            $this->defData['blogs'][$key]->author = $this->commonModel->selectOne('users', ['id' => $blog->author], 'firstname,sirname');
        }

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true]);
        $this->defData['tagInfo'] = $this->commonModel->selectOne('tags', ['seflink' => $seflink]);
        $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['tagInfo']->id, 'tag');

        return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/tags', $this->defData);
    }

    public function category(string $seflink)
    {
        $this->defData['category'] = $this->commonModel->selectOne('categories', ['seflink' => $seflink]);
        if (empty($this->defData['category'])) return show_404();

        $seo = !empty($this->defData['category']->seo) ? json_decode($this->defData['category']->seo, false) : (object) [];
        if (!is_object($seo)) $seo = (object) [];
        $this->defData['category']->seo = $seo;

        $keywords = [];
        if (!empty($seo->keywords) && is_array($seo->keywords)) {
            foreach ($seo->keywords as $keyword) {
                if (isset($keyword->value)) $keywords[] = $keyword->value;
            }
        }

        $description = $seo->description ?? '';
        $coverImage = $seo->coverImage ?? '';

        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            $this->defData['category']->title,
            $description,
            'category/' . $seflink,
            ['keywords' => $keywords],
            $coverImage
        );

        $this->defData['schema'] = $this->ci4msseoLibrary->ldPlusJson('Organization', [
            'url' => site_url(), 'logo' => $this->defData['settings']->logo ?? '',
            'name' => $this->defData['settings']->siteName ?? '',
            'children' => [
                'ContactPoint' => [
                    'ContactPoint' => [
                        'telephone' => $this->defData['settings']->company->phone ?? '',
                        'contactType' => 'customer support'
                    ]
                ]
            ],
            'sameAs' => array_map(fn($sN) => $sN['link'] ?? '', (array)($this->defData['settings']->socialNetwork ?? []))
        ]);

        $perPage = 12;
        $page = (int) $this->request->getUri()->getSegment(3, 1);
        $offset = ($page - 1) * $perPage;

        $this->defData['blogs'] = $this->ci4msModel->categoryList(['categories_id' => $this->defData['category']->id, 'isActive' => true], $perPage, $offset);
        $totalBlogs = count($this->defData['blogs']);

        $pager = \Config\Services::pager();
        $this->defData['pager'] = $pager->makeLinks($page, $perPage, $totalBlogs, $this->defData['settings']->templateInfos->path, 3);
        $this->defData['pager_info_text'] = (object) [
            'total_products' => $totalBlogs,
            'start' => ($page - 1) * $perPage + 1,
            'end' => min($page * $perPage, $totalBlogs),
        ];

        $this->defData['dateI18n'] = new Time();
        $modelTag = new AjaxModel();

        foreach ($this->defData['blogs'] as $key => $blog) {
            $this->defData['blogs'][$key]->tags = $modelTag->limitTags_ajax(['tags_pivot.piv_id' => $blog->id]);
            $this->defData['blogs'][$key]->author = $this->commonModel->selectOne('users', ['id' => $blog->author], 'firstname,sirname');
        }

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true]);
        $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['category']->id, 'category');

        return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/list', $this->defData);
    }

    // --- Comments (AJAX) ---
    public function newComment()
    {
        if (!$this->request->isAJAX()) return $this->failForbidden();

        $valData = [
            'comFullName' => ['label' => 'Full name', 'rules' => 'required'],
            'comEmail' => ['label' => 'E-mail', 'rules' => 'required|valid_email'],
            'comMessage' => ['label' => 'Join the discussion and leave a comment!', 'rules' => 'required'],
            'captcha' => ['Captcha' => 'Captcha', 'rules' => 'required']
        ];

        if (!$this->validate($valData)) return $this->fail($this->validator->getErrors());

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
                'blog_id' => $this->request->getPost('blog_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'comFullName' => $this->request->getPost('comFullName'),
                'comEmail' => $this->request->getPost('comEmail'),
                'comMessage' => $checked
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
        if (!$this->request->isAJAX()) return $this->failForbidden();

        $valData = ['comID' => ['label' => 'Comment', 'rules' => 'required']];
        if (!$this->validate($valData)) return $this->fail($this->validator->getErrors());

        return $this->respond([
            'display' => view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/replies', [
                'replies' => $this->commonModel->lists('comments', '*', ['parent_id' => $this->request->getPost('comID')])
            ])
        ], 200);
    }

    public function loadMoreComments()
    {
        if (!$this->request->isAJAX()) return $this->failForbidden();

        $valData = [
            'blogID' => ['label' => 'Blog ID', 'rules' => 'required|string'],
            'skip' => ['label' => 'data-skip', 'rules' => 'required|is_natural_no_zero']
        ];

        if (!empty($this->request->getPost('comID'))) {
            $valData['comID'] = ['label' => 'Comment ID', 'rules' => 'required|string'];
        }

        if (!$this->validate($valData)) return $this->fail($this->validator->getErrors());

        helper('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/funcs');

        $data = ['blog_id' => $this->request->getPost('blogID')];
        if (!empty($this->request->getPost('comID'))) {
            $data['parent_id'] = $this->request->getPost('comID');
        }

        $comments = $this->commonModel->lists('comments', '*', $data, 'id ASC', 5, (int)$this->request->getPost('skip'));

        return $this->respond([
            'display' => view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/blog/loadMoreComments', [
                'comments' => $comments,
                'blogID' => $this->request->getPost('blogID')
            ]),
            'count' => count($comments)
        ], 200);
    }

    public function commentCaptcha()
    {
        if (!$this->request->isAJAX()) return $this->failForbidden();

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

    // --- Public Auth: Login & Register (Frontend) ---

    public function register()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/register', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function registerPost()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = [
            'firstname' => 'required|min_length[2]',
            'sirname'   => 'required|min_length[2]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
            'captcha'   => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // ✅ Validate CAPTCHA BEFORE processing
        $userAnswer = (int) $this->request->getPost('captcha');
        $correctAnswer = (int) session()->get('math_captcha_answer');
        if ($userAnswer !== $correctAnswer) {
            return redirect()->back()->withInput()->with('error', lang('Auth.badCaptcha') ?: 'Incorrect CAPTCHA answer.');
        }

        $data = [
            'firstname'     => $this->request->getPost('firstname'),
            'sirname'       => $this->request->getPost('sirname'),
            'email'         => $this->request->getPost('email'),
            'username'      => null,
            'password_hash' => $authLib->setPassword($this->request->getPost('password')),
            'group_id'      => 2,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        if ($this->commonModel->create('users', $data)) {
            // ✅ Clear CAPTCHA after success
            session()->remove('math_captcha_answer');
            return redirect()->to('/login')->with('message', 'Registration successful!');
        }

        return redirect()->back()->with('error', 'An error occurred during registration.');
    }

    public function login()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/login', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function loginPost()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
            'captcha'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // ✅ Validate CAPTCHA BEFORE login attempt
        $userAnswer = (int) $this->request->getPost('captcha');
        $correctAnswer = (int) session()->get('math_captcha_answer');
        if ($userAnswer !== $correctAnswer) {
            return redirect()->back()->withInput()->with('error', lang('Auth.badCaptcha') ?: 'Incorrect CAPTCHA answer.');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        if ($authLib->attempt(['email' => $email, 'password' => $password], $remember)) {
            session()->remove('math_captcha_answer');
            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', $authLib->error() ?? 'Invalid credentials.');
    }

    public function logout()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        $authLib->logout();
        return redirect()->to('/');
    }
}