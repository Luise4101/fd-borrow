<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function authorizeCall() {
        return Socialite::driver('laravelpassport')->redirect();
    }

    public function callback() {
        $responseLoginApp = Http::withOptions(['verify' => false])
            ->post('https://api.dhammakaya.network/api/Login/LoginAPP', [
                'UserName' => 'FDNet',
                'Password' => 'evbbsmxjwy'
            ]);
        if($responseLoginApp->successful()) {
            $dataLogin = $responseLoginApp->json();
            $token = $dataLogin['Token'];
            session(['hrapi_token' => $token]);
        }
        $authADuser = Socialite::driver('laravelpassport')->user();
        $responsePersonData = Http::withOptions(['verify' => false])->withToken($token)
            ->get('https://api.dhammakaya.network/api/Person/getPersonAdInternal', [
                'aduser' => $authADuser->name
            ]);
        if($responsePersonData->successful()) {
            $dataResponse = $responsePersonData->json();
            $dataData = $dataResponse['Data'];
            $dataUser = $dataData[0];
        }
        $user = User::updateOrCreate(['name' => $dataUser['Aduser']], [
            'email' => $dataUser['Email'],
            'fullname' => $dataUser['Fullname'],
            'password' => ''
        ]);
        Filament::auth()->login($user);
        return redirect('/admin');
    }

    public function logout(Request $request) {
        Filament::auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
