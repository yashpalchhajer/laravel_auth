<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
Use Socialite;
use Auth;
use Exception;

class SocialAuthGoogleController extends Controller
{
    public function redirectToProvider(){
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromProvider(){
        try{
            $googleUser = Socialite::driver('google')->user();

            $existsUser = User::where('email',$googleUser->email)->first();

            if($existsUser){
                Auth::loginUsingId($existsUser->id);
            }else{
                $user = new User();
                $user->name = $googleUser->user;
                $user->email = $googleUser->email;
                $user->password = md5(1,1000);
                $user->save();
                Auth::loginUsingId($user->id);
            }
            return redirect()->to('/home');
        }catch(\Exception $e){
           return 'error';
        }
    }
}
