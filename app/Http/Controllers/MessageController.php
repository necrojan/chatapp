<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Message;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    /**
     * @param User $user
     *
     * @return Builder[]|Collection
     */
    public function show(User $user)
    {
        return Message::with('user')
            ->where(['user_id' => auth()->id(), 'receiver_id' => $user->id, 'is_archive' => false])
            ->orWhere(function ($query) use ($user) {
                $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id(), 'is_archive' => false]);
            })
            ->get();
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'message' => 'required',
        ]);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
        ]);

        event(new NewMessage($message->load('user')));

        return [
            'message' => $message,
        ];
    }
}