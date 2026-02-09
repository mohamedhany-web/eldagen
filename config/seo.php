<?php

return [

    /*
    |--------------------------------------------------------------------------
    | بيانات SEO الأساسية
    |--------------------------------------------------------------------------
    */

    'site_name' => env('SEO_SITE_NAME', 'منصة الطارق في الرياضيات'),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'منصة تعليمية للرياضيات - مستر طارق الداجن. كورسات، امتحانات، ومراجعات لجميع المراحل.'),
    'default_image' => env('SEO_DEFAULT_IMAGE', null), // URL صورة افتراضية للمشاركة (مثلاً https://yoursite.com/images/og-default.jpg)
    'twitter_handle' => env('SEO_TWITTER_HANDLE', null),
    'locale' => 'ar_SA',
    'locale_alternate' => ['ar'],

];
