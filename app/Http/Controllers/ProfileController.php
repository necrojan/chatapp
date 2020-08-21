<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @return Factory|View
     */
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    /**
     * @param UpdateProfileRequest $request
     * @param User                 $user
     *
     * @return RedirectResponse|Redirector
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        $request->validated();

        $data = $request->all();
        $password = $request->input('password');

        if ($password !== $user->password) {
            $data['password'] = Hash::make($password);
        }

        if ($request->hasFile('photo')) {
            $imageName = $request->file('photo')->getClientOriginalName();
            $request->file('photo')->storeAs('public/images', $imageName);
            $data['photo'] = $imageName;
        }

        $user->update($data);

        return redirect(route('profile'));
    }

    /**
     * @return Factory|View
     */
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }
}
