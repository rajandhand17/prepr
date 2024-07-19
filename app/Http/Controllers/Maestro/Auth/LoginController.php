<?php

namespace App\Http\Controllers\Maestro\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    protected $redirectTo = '/maestro/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard.index');
        }

        return view('maestro.auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $validation_array = [
            'email'    => 'required|max:255',
            'password' => 'required',
        ];

        $validation = Validator::make($request->all(), $validation_array);
        if ($validation->fails()) {
            return redirect()->route('login')->withErrors($validation)->withInput()->with('error', 'Sorry. '.$validation->messages()->first());
        }

        if (auth()->attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::guard('maestro')->user();
            if ($user && $user->hasRole('super_admin')) {
                return redirect()->route('dashboard.index');
            } else {
                return redirect()->route('login')->with('error', "You don't have login access.");
            }
        } else {
            return redirect()->route('login')->with('error', 'Sorry , Entered email and password is wrong.');
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('login');
    }
}
