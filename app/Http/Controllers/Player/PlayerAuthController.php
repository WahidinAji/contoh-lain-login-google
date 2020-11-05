<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Model\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Session;
use Laravel\Socialite\Facades\Socialite;


class PlayerAuthController extends Controller
{
    public function index()
    {
        return \view('player.auth.login'); //view login
    }
    public function register()
    {
        return \view('player.auth.register'); //view register
    }
    public function postLogin(Request $request)
    {
        \request()->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        // $player = Player::where('email', $request->email)->first();
        if (Auth::guard('player')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            return \redirect('dashboard'); //redirect to url link dashboard
        } else {
            return Redirect::to("login"); //routing login jika user tidak ada
        }
    }
    public function postRegister(Request $request)
    {
        \request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:players',
            'password' => 'required|min:8',
        ]);
        // $data = $request->all();
        // $check = $this->create($data);
        Player::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // \dd(Auth::guard('player')->user());
        if (Auth::guard('player')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            return \redirect('dashboard')->with(['success' => 'Register success']); //redirect to url link dashboard
        } else {
            return Redirect::to("login"); //routing login jika user tidak ada
        }
        // return \redirect('dashboard'); //redirect to url link dashboard
        // return Redirect::to("dashboard")->withSuccess('Great! U have successfully loggedin'); //routing dashboard
    }
    public function dashboard()
    {
        if (Auth::guard('player')->check()) {
            return view('player.dashboard'); //view dashboard
        } else {
            return Redirect('login')->with('msg', 'Anda harus login'); //routing login
        }
    }
    public function create(array $data)
    {
        return Player::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    public function logout()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login'); //routing login
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function callbackPlayer()
    {
        // jika user masih login lempar ke home
        if (Auth::guard('player')->check()) {
            return redirect('dashboard');
        }
        $oauthUser = Socialite::driver('google')->user();
        $player = Player::where('google_id', $oauthUser->id)->first();
        if ($player) {
            // Auth::loginUsingId($player->id, \true);
            Auth::guard('player')->login($player);
            return redirect('dashboard');
        } else {
            $newUser = Player::create([
                'google_id' => $oauthUser->id,
                'name' => $oauthUser->name,
                'email' => $oauthUser->email,
                'ava_url' => $oauthUser->avatar,
                // password tidak akan digunakan ;)
                'password' => md5($oauthUser->token),
            ]);
            // Auth::login($newUser);
            Auth::guard('player')->login($newUser);
            return redirect('dashboard');
        }
    }
}
