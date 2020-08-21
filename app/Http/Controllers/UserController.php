<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @return User[]|Collection
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return response()->json($user);
    }
}