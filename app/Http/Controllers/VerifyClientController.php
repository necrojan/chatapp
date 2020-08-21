<?php

namespace App\Http\Controllers;

use App\Events\ClientVerification;
use App\Events\ClientVerified;
use App\Notifications\VerifyClient;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyClientController extends Controller
{
    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function index(User $user)
    {
        $code = mt_rand(111111, 999999);

        $user->notify(new VerifyClient($code));

        $user->client()->update([
            'code' => $code
        ]);

        event(new ClientVerification($user));

        return response()->json('success');
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return bool
     */
    public function store(User $user, Request $request)
    {
        $code = $request->input('code');

        if ($user->client->code != $code) {
            return false;
        }

        $user->client()->update([
            'is_verified' => true
        ]);

        event(new ClientVerified($user->client->load('user')));

        return true;
    }
}
