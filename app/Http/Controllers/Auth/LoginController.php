<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Message;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * {@inheritDoc}
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response|JsonResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (!App::environment(['local', 'staging'])) {
            $username = $request->input('username');
            $credentials = explode('@', $username);
            $ldap = $this->execLdap($request);

            if (!$ldap) {
                return $this->sendFailedLoginResponse($request);
            }
            $ldap = $ldap[0];
            $name = isset($ldap['cn']) ? $ldap['cn'][0] : ucfirst($credentials[0]);

            if ($credentials[0] !== 'chattest') {
                $email = str_replace('smtp:', '', $ldap['proxyaddresses'][0]);
                if (!$email) {
                    $email = $ldap['mail'][0];
                }
            } else {
                $email = 'chattest@chat.com';
            }

            $user = User::firstOrCreate(['username' => $credentials[0]], [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($request->input('password'))
            ]);
            $user->assignRoleTitle('admin');

            Auth::login($user);

            return redirect()->intended('/admin');
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts')
            && $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @param Request $request
     *
     * @return array|bool|false|resource
     */
    public function execLdap(Request $request)
    {
        $userName = $request->input('username');
        $password = $request->input('password');

        $userName = explode('@', $userName);

        $ldapConnection = ldap_connect(config('ldap.ip'), config('ldap.port'));

        if ($ldapConnection) {
            $ldapBind = @ldap_bind($ldapConnection, config('ldap.prefix').'\\'.$userName[0], $password);

            if (!$ldapBind) {
                return false;
            }

            $searchFilter = "(sAMAccountName=$userName[0])";
            $ldapDn = config('ldap.dn');

            $result = ldap_search($ldapConnection, $ldapDn, $searchFilter);
            $result = ldap_get_entries($ldapConnection, $result);

            if ($ldapBind) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        if ($request->user()->hasRole('client')) {
            $userId = $request->user()->id;

            $messages = Message::query()
                ->where(['user_id' => $userId])
                ->orWhere(function ($query) use ($userId) {
                    $query->where(['receiver_id' => $userId]);
                });

            foreach ($messages->get() as $message) {
                $message->update(['is_archive' => true]);
            }
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }
}
