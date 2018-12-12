<?php

Route::post('/auth/login', '\\Bahraminekoo\\Larauth\\Controllers\\LoginController@login');
Route::post('/auth/register', '\\Bahraminekoo\\Larauth\\Controllers\\SignUpController@register');
Route::get('/auth/verify-email/{email}/{hash}', '\\Bahraminekoo\\Larauth\\Controllers\\SignUpController@verifyEmail');































