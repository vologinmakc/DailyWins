<?php

namespace App\Http\Controllers\Captcha;

use App\Http\Controllers\BaseController;
use App\Services\Captcha\CaptchaService;
use Illuminate\Support\Facades\DB;

class CaptchaController extends BaseController
{
    private $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    public function generate()
    {
        DB::beginTransaction();
        try {
            $captchaData = $this->captchaService->generateCaptcha();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $this->response($captchaData);
    }
}
