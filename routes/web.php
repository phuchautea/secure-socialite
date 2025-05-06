<?php

use Illuminate\Support\Facades\Route;
use SecureSocialite\Controllers\SocialAuthController;

Route::get('/auth/social/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/social/callback', [SocialAuthController::class, 'callback']);
