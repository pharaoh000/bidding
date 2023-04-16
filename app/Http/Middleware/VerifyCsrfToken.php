<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/provider/request/*',
        '/provider/profile/available',
        '/stripe/account',
        '/contact/us',
        '/account/kit',
        '/successConverge',
        '/cancelConverge'
    ];
}
