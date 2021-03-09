<?php

namespace App\Models;

class QuizType extends Model
{

    const CONSOLE_NO = 0;
    const CONSOLE_YES = 1;

    const IOS_NO = 0;
    const IOS_YES = 1;

    const ANDROID_NO = 0;
    const ANDROID_YES = 1;

    const WEB_NO = 0;
    const WEB_YES = 1;

    const ANSWERS_TYPES_BASIC = 0;

    protected $fillable = [
        'console',
        'ios',
        'android',
        'web',
        'answers_types',
    ];

}
