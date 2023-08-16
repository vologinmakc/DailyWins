<?php

namespace App\Models\Captcha;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $value
 * @property string $token
 * @property string $expires_at
 */
class Captcha extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'value',
        'token',
        'expires_at'
    ];
}
