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

    // We will move methods here in the next step
}