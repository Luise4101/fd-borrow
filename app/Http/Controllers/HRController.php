<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HRController extends Controller
{
    public function fetchAndStoreUserData(string $aduser = null, string $token = null): void {
        try {
            if(!$token) {
                $responseLoginApp = Http::withOptions(['verify' => false])->post(env('API_HR_LOGIN'), [
                    'UserName' => env('API_HR_USERNAME'),
                    'Password' => env('API_HR_PASSWORD')
                ]);
                if(!$responseLoginApp->successful()) {
                    throw new \Exception('Failed to authenticate with HR.');
                }
                $dataLogin = $responseLoginApp->json();
                $token = $dataLogin['Token'];
                session(['hrapi_token' => $token]);
            }
            if($aduser) {
                $responsePersonData = Http::withOptions(['verify' => false])->withToken($token)->get(env('API_HR_PERSON'), [
                    'aduser' => $aduser
                ]);
                if($responsePersonData->successful()) {
                    $dataResponse = $responsePersonData->json();
                    $dataUser = $dataResponse['Data'][0] ?? [];
                    session([
                        'user_data' => [
                            'aduser' => $dataUser['Aduser'] ?? null,
                            'email' => $dataUser['Email'] ?? null,
                            'fullname' => $dataUser['Fullname'] ?? null,
                            'mobile' => $dataUser['Mobile'] ?? null,
                            'lineid' => $dataUser['Lineid'] ?? null,
                            'qsamnak' => $dataUser['DepartmentId'] ?? null,
                            'csamnak' => $dataUser['Department'] ?? null,
                            'qsection' => $dataUser['SectionId'] ?? null,
                            'csection' => $dataUser['Section'] ?? null,
                            'qkong' => $dataUser['OfficeId'] ?? null,
                            'ckong' => $dataUser['Office'] ?? null
                        ]
                    ]);
                } else {
                    throw new \Exception('Failed to retrieve person data from HR.');
                }
            }
            if($aduser) {
                $responseApproveData = Http::withOptions(['verify' => false])->withToken($token)->get(env('API_HR_APPROVE'), [
                    'aduser' => $aduser
                ]);
                if($responseApproveData->successful()) {
                    $dataResponse = $responseApproveData->json();
                    $dataApprove = $dataResponse['Data'][0] ?? [];
                    session([
                        'approve_data' => [
                            'qhead' => $dataApprove['HeadLogin'] ?? null,
                            'chead' => $dataApprove['HeadFullName'] ?? null,
                            'email' => $dataApprove['HeadEmail'] ?? null
                        ]
                    ]);
                } else {
                    throw new \Exception('Failed to retrieve approve data from HR.');
                }
            }
        } catch(\Exception $e) {
            Log::error('Error fetching user data: '.$e->getMessage());
            session()->forget(['user_data', 'approve_data']);
        }
    }

    public function fetchApproveBorrowData($aduser) {
        try {
            $token = session('hrapi_token');
            if(!$token) {
                throw new \Exception('HR token is missing.');
            }
            $responseApproveData = Http::withOptions(['verify' => false])->withToken($token)->get(env('API_HR_APPROVE'), [
                'aduser' => $aduser
            ]);
            if(!$responseApproveData->successful()) {
                throw new \Exception('Failed to retrieve approve data from HR.');
            }
            $dataResponse = $responseApproveData->json();
            $dataApprove = $dataResponse['Data'][0] ?? [];
            if($dataApprove) {
                session([
                    'approve_each_head' => [
                        'qhead' => $dataApprove['HeadLogin'] ?? null,
                        'chead' => $dataApprove['HeadFullName'] ?? null,
                        'email' => $dataApprove['HeadEmail'] ?? null
                    ]
                ]);
                return session('approve_each_head');
            }
        } catch(\Exception $e) {
            Log::error('Error fetching approve data: '.$e->getMessage());
            return null;
        }
    }
}