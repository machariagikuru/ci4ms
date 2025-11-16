<?php

namespace Modules\Logs\Controllers;

use CILogViewer\CILogViewer;
use CodeIgniter\HTTP\RedirectResponse;

class Logs extends \Modules\Backend\Controllers\BaseController
{
    public function index(): mixed
    {
        $request = \Config\Services::request();
        $logViewer = new CILogViewer();

        // Handle log deletion in the controller — before rendering the view
        if (!is_null($request->getGet('del'))) {
            $logViewer->deleteFiles(base64_decode($request->getGet('del')));
            return redirect()->to(site_url('backend/logs'));
        }

        // Pass logViewer to view only for display (no redirects from showLogs!)
        $this->defData['logViewer'] = $logViewer;
        return view('Modules\Logs\Views\list', $this->defData);
    }
}