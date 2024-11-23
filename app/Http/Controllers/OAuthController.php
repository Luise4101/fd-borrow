<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\HRController;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function authorizeCall() {
        return Socialite::driver('laravelpassport')->redirect();
    }

    public function callback() {
        try {
            $authADuser = Socialite::driver('laravelpassport')->user();
            $aduser = $authADuser->name;
            (new HRController())->fetchAndStoreUserData($aduser);
            $userData = session('user_data');
            if(!$userData) {
                throw new \Exception('User data is missing.');
            }
            $user = User::updateOrCreate(['name' => $userData['aduser']], [
                'email' => $userData['email'],
                'fullname' => $userData['fullname'],
                'password' => ''
            ]);
            Filament::auth()->login($user);
            return redirect('/admin');
        } catch(\Exception $e) {
            abort(500, 'An unexpected error occurred: '.$e->getMessage());
        }
    }

    public function logout(Request $request) {
        Filament::auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
