<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Libraries\CommonLibrary;
use App\Models\Ci4ms;
use CodeIgniter\I18n\Time;
use Modules\Backend\Models\AjaxModel;
use Modules\Users\Models\UserscrudModel;

class BlogController extends BaseController
{
    private CommonLibrary $commonLibrary;
    private Ci4ms $ci4msModel;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        $this->ci4msModel = new Ci4ms();
        // $this->defData, $this->commonModel, etc. inherited from BaseController
    }

    public function browseTags(int $page = 1)
    {
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $this->defData['tags'] = $this->commonModel->lists('tags', '*', [], 'tag ASC', $perPage, $offset);
        $total = $this->commonModel->count('tags');

        $pager = \Config\Services::pager();
        $this->defData['pager'] = $pager->makeLinks($page, $perPage, $total, $this->defData['settings']->templateInfos->path, 2);

        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            'Tags',
            'Browse all tags',
            'tags',
            ['keywords' => ['tags', 'topics']],
            ''
        );

        return view('templates/default/content/tags', $this->defData);
    }

    public function browseCategories(int $page = 1)
    {
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true], 'title ASC', $perPage, $offset);
        $total = $this->commonModel->count('categories', ['isActive' => true]);

        $pager = \Config\Services::pager();
        // ✅ Use segment 2 (not 3)
        $this->defData['pager'] = $pager->makeLinks($page, $perPage, $total, $this->defData['settings']->templateInfos->path, 2);

        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            'Categories',
            'Browse all categories',
            'categories',
            ['keywords' => ['categories', 'blog topics']],
            ''
        );

        return view('templates/default/content/categories', $this->defData);
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
    

        $data['similarPosts'] = $this->commonModel->db
        ->table('blog')
        ->select('id, title, seflink, created_at')
        ->where('id !=', $currentBlogId)
        ->where('isActive', 1)
        ->orderBy('RAND()') // or use tag/category matching for true similarity
        ->limit(5)
        ->get()
        ->getResult();
    }

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
        // Normalize SEO
        $seo = !empty($blog->seo) ? json_decode($blog->seo, false) : (object) [];
        if (!is_object($seo)) $seo = (object) [];
        $this->defData['blogs'][$key]->seo = $seo;

        // Tags & Author
        $this->defData['blogs'][$key]->tags = $modelTag->limitTags_ajax(['piv_id' => $blog->id]);
        $this->defData['blogs'][$key]->author = $this->commonModel->selectOne('users', ['id' => $blog->author], 'firstname,sirname');
            }

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true]);
        $this->defData['tagInfo'] = $this->commonModel->selectOne('tags', ['seflink' => $seflink]);
        $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['tagInfo']->id, 'tag');
        $this->defData['allTags'] = $this->commonModel->lists('tags', '*', [], 'tag ASC');

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

    // Blog methods will go here
}