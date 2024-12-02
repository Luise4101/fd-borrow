<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\BorrowController;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(OAuthController::class)->group(function() {
    Route::get('/admin/login', 'authorizeCall')->name('filament.admin.auth.login');
    Route::get('/admin/callback', 'callback')->name('callback');
    Route::post('/logout', 'logout')->name('filament.admin.auth.logout');
});

Route::controller(BorrowController::class)->group(function() {
    Route::get('/borrow/access', 'accessWithToken')->name('borrow.access');
});

Route::get('/testMail', function() {
    return view('testSendMail');
});
Route::post('/testMail/sendApi', function() {
    $to = request('to');
    $subject = request('subject');
    $message = request('message');

    $responseApi = Http::withOptions(['verify' => false])
        ->post(env('API_SEND_MAIL'), [
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ]);
    return $responseApi->json();
})->name('sendmail');