<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmailService {
    protected $apiUrl;

    public function __construct() {
        $this->apiUrl = env('API_SEND_MAIL');
    }
    public function sendEmail($to, $subject, $message) {
        $response = Http::withOptions(['verify' => false])->post($this->apiUrl, [
            'to' => $to,
            'subject' => $subject,
            'message' => $message
        ]);
        return $response->json();
    }
}