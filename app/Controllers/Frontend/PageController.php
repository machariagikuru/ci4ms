<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Libraries\CommonLibrary;

class PageController extends BaseController
{
    private CommonLibrary $commonLibrary;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        // $this->defData, $this->commonModel, etc. inherited from BaseController
    }

    // --- Structured Homepage (Partials) ---
    public function home()
    {
        // Fetch editable sections (navbar, hero, etc.)
        $sections = ['navbar', 'hero', 'features', 'testimonials'];
        foreach ($sections as $section) {
            $this->defData[$section] = $this->commonModel->selectOne('pages', ['seflink' => $section]);
        }

        // Fetch dynamic data
        $this->defData['latestBlogs'] = $this->commonModel->lists('blog', '*', ['isActive' => true], 'id DESC', 3);
        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true], 'title ASC');
        $this->defData['tags'] = $this->commonModel->lists('tags', '*', [], 'tag ASC', 30); // top 30 tags

        // SEO
        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            'Home Page',
            'Welcome to our site',
            '/',
            ['keywords' => ['home', 'welcome']],
            ''
        );

        return view('templates/default/home', $this->defData);
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
}

