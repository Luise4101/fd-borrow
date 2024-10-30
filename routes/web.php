<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin');
});

Route::controller(OAuthController::class)->group(function() {
    Route::get('/admin/login', 'authorizeCall')->name('filament.admin.auth.login');
    Route::get('/admin/callback', 'callback')->name('callback');
    Route::post('/logout', 'logout')->name('filament.admin.auth.logout');
});

Route::get('/testMail', function() {
    return view('testSendMail');
});
Route::post('/testMail/sendApi', function() {
    $to = request('to');
    $subject = request('subject');
    $message = request('message');

    $responseApi = Http::withOptions(['verify' => false])
        ->post('https://fdnet.dhammakaya.network/application/api/send_mail_approve.php', [
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ]);
    return $responseApi->json();
})->name('sendmail');