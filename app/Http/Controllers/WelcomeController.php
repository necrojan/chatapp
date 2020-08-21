<?php

namespace App\Http\Controllers;

use App\Events\NewPool;
use App\Events\RestorePool;
use App\Pool;
use App\Services\ConnectWise;
use App\Services\Recaptcha;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * @var ConnectWise
     */
    private $cw;

    private $captcha;

    public function __construct()
    {
        $this->cw = new ConnectWise();
        $this->captcha = new Recaptcha();
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function loginForm()
    {
        if (auth()->user()) {
            return redirect('/chat');
        }

        return view('welcome');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'full_name' => 'required|max:255',
            'company' => 'required|max:255',
            'phone' => 'nullable|regex:/(0)[0-9]/|not_regex:/[a-z]/|min:9',
            'recaptcha' => 'required',
        ]);
        $email = $request->input('email');

        $data = [
            'secret' => config('recaptcha.secret_key'),
            'response' => $request->input('recaptcha'),
        ];

        $response = $this->captcha->submit($data);
        if ($response['success'] != true) {
            return redirect('/');
        }

        if (config('cw.integrate')) {
            $emailList = $this->cw->isEmailOnList($email);

            if (!$emailList) {
                return view('error.emailNotFound');
            }

            $userArray = $this->cw->getNameAndCompany($email);

            return $this->createUser($request, $userArray);
        }

        return $this->createUser($request);
    }

    /**
     * @param Request $request
     * @param null    $user
     *
     * @return array
     */
    private function userSwitchOnCreate(Request $request, $user = null)
    {
        if ($user) {
            $email = $user['communicationItems'][0]['value'];
            $first = $user['firstName'];
            $last = $user['lastName'];
            $company = $user['company']['name'];
            $phone = $user['defaultPhoneNbr'];
        } else {
            $email = $request->input('email');
            $data = $request->input('full_name');
            $fullNameArray = explode(' ', $data);

            if (count($fullNameArray) > 2) {
                $first = conArr($fullNameArray)[0];
                $last = conArr($fullNameArray)[1];
            } else {
                $first = $fullNameArray[0];
                $last = $fullNameArray[1] ?? '';
            }

            $company = $request->input('company');
            $phone = $request->input('phone');
        }

        return [$email, $first, $last, $company, $phone];
    }

    /**
     * @param Request $request
     * @param null    $user
     *
     * @return RedirectResponse|Redirector
     */
    private function createUser(Request $request, $user = null)
    {
        list($email, $first, $last, $company, $phone) = $this->userSwitchOnCreate($request, $user);

        $name = $first . $last;

        $user = User::firstOrCreate(['email' => $email], [
            'name' => trim(ucwords($name)),
            'username' => $email,
            'password' => Hash::make($email),
        ]);

        if ($user->hasRole(['admin', 'agent'])) {
            abort(response()->view('error.unauthorizedUser'));
        }

        $user->assignRoleTitle('client');

        $client = $user->client()->firstOrCreate([],
            [
                'machine' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ],
                'company' => $company,
                'phone' => $phone,
            ]);

        $pool = Pool::withTrashed()
            ->firstWhere('client_id', $client->id);

        if (!$pool || !$pool->trashed()) {
            Pool::firstOrCreate(['client_id' => $client->id]);

            event(new NewPool($client->load('user')));
        }

        auth()->login($user);

        return redirect('/chat');
    }
}
