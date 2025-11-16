<?php

namespace Modules\Blog\Controllers;

use JasonGrimes\Paginator;

class Categories extends \Modules\Backend\Controllers\BaseController
{
    public function index()
    {
        $totalItems = $this->commonModel->count('categories', []);
        $itemsPerPage = 20;
        $currentPage = (int) $this->request->getUri()->getSegment(4, 1);
        $urlPattern = '/backend/blogs/categories/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $paginator->setMaxPagesToShow(5);
        $offset = ($currentPage - 1) * $itemsPerPage;

        $this->defData['paginator'] = $paginator;
        $this->defData['categories'] = $this->commonModel->lists('categories', '*', [], 'id ASC', $itemsPerPage, $offset);
        return view('Modules\Blog\Views\categories\list', $this->defData);
    }

    public function new()
    {
        if ($this->request->is('post')) {
            $valData = [
                'title' => ['label' => lang('Backend.title'), 'rules' => 'required'],
                'seflink' => ['label' => lang('Backend.url'), 'rules' => 'required'],
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $valData['pageimg'] = ['label' => lang('Backend.coverImgURL'), 'rules' => 'required'];
                $valData['pageIMGWidth'] = ['label' => lang('Backend.coverImgWith'), 'rules' => 'required|is_natural_no_zero'];
                $valData['pageIMGHeight'] = ['label' => lang('Backend.coverImgHeight'), 'rules' => 'required|is_natural_no_zero'];
            }

            if (!$this->validate($valData)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->commonModel->isHave('categories', ['seflink' => $this->request->getPost('seflink')]) !== 0) {
                return redirect()->back()->withInput()->with('error', lang('Backend.slugExists', [$this->request->getPost('seflink')]));
            }

            $data = [
                'title' => $this->request->getPost('title'),
                'seflink' => $this->request->getPost('seflink'),
                'isActive' => (bool) $this->request->getPost('isActive'),
            ];

            if (!empty($this->request->getPost('parent'))) {
                $data['parent'] = $this->request->getPost('parent');
            }

            $seo = [];
            if (!empty($this->request->getPost('description'))) {
                $seo['description'] = $this->request->getPost('description');
            }
            if (!empty($this->request->getPost('pageimg'))) {
                $seo['coverImage'] = $this->request->getPost('pageimg');
                $seo['IMGWidth'] = $this->request->getPost('pageIMGWidth');
                $seo['IMGHeight'] = $this->request->getPost('pageIMGHeight');
            }
            if (!empty($this->request->getPost('keywords'))) {
                $seo['keywords'] = json_decode($this->request->getPost('keywords'), true);
            }

            $data['seo'] = json_encode($seo, JSON_UNESCAPED_UNICODE);

            if ($this->commonModel->create('categories', $data)) {
                return redirect()->route('categories', [1])->with('message', lang('Backend.created', [$data['title']]));
            }

            return redirect()->back()->withInput()->with('error', lang('Backend.notCreated', [$data['title']]));
        }

        $this->defData['categories'] = $this->commonModel->lists('categories');
        return view('Modules\Blog\Views\categories\create', $this->defData);
    }

    public function edit(string $id)
    {
        if ($this->request->is('post')) {
            $valData = [
                'title' => ['label' => lang('Backend.title'), 'rules' => 'required'],
                'seflink' => ['label' => lang('Backend.url'), 'rules' => 'required'],
            ];

            if (!empty($this->request->getPost('pageimg'))) {
                $valData['pageimg'] = ['label' => lang('Backend.coverImgURL'), 'rules' => 'required'];
                $valData['pageIMGWidth'] = ['label' => lang('Backend.coverImgWith'), 'rules' => 'required|is_natural_no_zero'];
                $valData['pageIMGHeight'] = ['label' => lang('Backend.coverImgHeight'), 'rules' => 'required|is_natural_no_zero'];
            }

            if (!$this->validate($valData)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $info = $this->commonModel->selectOne('categories', ['id' => $id]);
            if (!$info) {
                return redirect()->route('categories', [1])->with('error', lang('Backend.recordNotFound'));
            }

            if ($info->seflink !== $this->request->getPost('seflink') && $this->commonModel->isHave('categories', ['seflink' => $this->request->getPost('seflink')]) !== 0) {
                return redirect()->back()->withInput()->with('error', lang('Backend.slugExists', [$this->request->getPost('seflink')]));
            }

            $data = [
                'title' => $this->request->getPost('title'),
                'seflink' => $this->request->getPost('seflink'),
                'isActive' => (bool) $this->request->getPost('isActive'),
            ];

            if (!empty($this->request->getPost('parent'))) {
                $data['parent'] = $this->request->getPost('parent');
            }

            $seo = [];
            if (!empty($this->request->getPost('description'))) {
                $seo['description'] = $this->request->getPost('description');
            }
            if (!empty($this->request->getPost('pageimg'))) {
                $seo['coverImage'] = $this->request->getPost('pageimg');
                $seo['IMGWidth'] = $this->request->getPost('pageIMGWidth');
                $seo['IMGHeight'] = $this->request->getPost('pageIMGHeight');
            }
            if (!empty($this->request->getPost('keywords'))) {
                $seo['keywords'] = json_decode($this->request->getPost('keywords'), true);
            }

            $data['seo'] = json_encode($seo, JSON_UNESCAPED_UNICODE);

            if ($this->commonModel->edit('categories', $data, ['id' => $id])) {
                return redirect()->route('categories', [1])->with('message', lang('Backend.updated', [$data['title']]));
            }

            return redirect()->back()->withInput()->with('error', lang('Backend.notUpdated', [$data['title']]));
        }

        $this->defData['infos'] = $this->commonModel->selectOne('categories', ['id' => $id]);
        if (!$this->defData['infos']) {
            return redirect()->route('categories', [1])->with('error', lang('Backend.recordNotFound'));
        }

        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['id!=' => $id]);

        // Safely decode SEO
        $seo = !empty($this->defData['infos']->seo) ? json_decode($this->defData['infos']->seo, false) : (object) [];
        if (!is_object($seo)) {
            $seo = (object) [];
        }

        // Ensure keywords exists and is a JSON string for the form (Tagify expects JSON string)
        $seo->keywords = json_encode(isset($seo->keywords) ? $seo->keywords : [], JSON_UNESCAPED_UNICODE);

        $this->defData['infos']->seo = $seo;

        return view('Modules\Blog\Views\categories\update', $this->defData);
    }

    public function delete(string $id)
    {
        if ($this->commonModel->remove('categories', ['id' => $id])) {
            return redirect()->route('categories', [1])->with('message', lang('Backend.deleted', ['#' . $id]));
        }
        return redirect()->route('categories', [1])->with('error', lang('Backend.notDeleted', ['#' . $id]));
    }
}