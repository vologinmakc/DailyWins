<?php

namespace App\Services\Captcha;

use App\Models\Captcha\Captcha;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

class CaptchaService
{
    public function generateCaptcha()
    {
        // Генерация значения и токена капчи
        $value = $this->generateRandomString();
        $token = bin2hex(random_bytes(16));

        // Сохранение капчи в базу данных
        Captcha::create([
            'token'      => $token,
            'value'      => $value,
            'expires_at' => Carbon::now()->addHour()
        ]);

        // Возвращаем изображение и токен
        return [
            'image' => $this->generateCaptchaImage($value),
            'token' => $token
        ];
    }

    /**
     * Генерируем случайную строку
     * @param $length
     * @return string
     */
    private function generateRandomString($length = 5)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    private function generateCaptchaImage($value)
    {
        // Создаем новое пустое изображение с белым фоном
        $img = Image::canvas(250, 100, '#ffffff');

        // Добавляем текст капчи на изображение
        $img->text($value, 125, 50, function ($font) {
            $font->file(public_path('fonts/BricolageGrotesque/static/BricolageGrotesque-Bold.ttf'));
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('center');
        });

        // Возвращаем изображение в Base64
        return 'data:image/png;base64,' . base64_encode($img->encode('png'));
    }

    public function validateCaptcha($token, $inputValue)
    {
        // Удаление просроченных записей
        $this->removeExpiredCaptchas();

        $captcha = Captcha::where('token', $token)->first();

        if (!$captcha) {
            return false;
        }

        if (Carbon::now()->greaterThan($captcha->expires_at)) {
            $captcha->delete();
            return false;
        }

        if ($captcha->value === $inputValue) {
            $captcha->delete();
            return true;
        }

        return false;
    }

    private function removeExpiredCaptchas()
    {
        Captcha::where('expires_at', '<', Carbon::now())->delete();
    }
}
