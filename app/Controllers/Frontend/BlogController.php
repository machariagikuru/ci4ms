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

    // Blog methods will go here
}