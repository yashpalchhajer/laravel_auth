<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider(){
        return Socialite::driver('google')->redirect();
    }

    public function handleCallbackProvider(){
        try{
            $user = Socialite::driver('google')->user();
            $existUser = User::where('email',$user->email)->first();
            if($existUser){
                auth()->login($existUser,true);
            }else{
                $newUser = new User();
                $newUser->name = $user->getName();
                $newUser->email = $user->getEmail();
                $newUser->password = md5('sec' . rand(1,100) . 'ret');
                $newUser->save();
                auth()->login($newUser,true);
            }
            return redirect()->to($this->redirectTo);
            
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return redirect()->to('/login');
        }
    }
}
