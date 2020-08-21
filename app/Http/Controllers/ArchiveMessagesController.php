<?php

namespace App\Http\Controllers;

use App\Types\ArchiveMessagesType;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveMessagesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        $users = User::with([
            'messages' => function ($query) {
                $query->where('is_archive', true);
            },
        ])
            ->orderBy('email', 'asc')
            ->paginate(ArchiveMessagesType::PAGINATION_COUNT);

        return view('messages.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'required|string|max:255'
        ]);

         return User::with([
            'messages' => function ($query) use ($request) {
                $query->where('is_archive', true)
                    ->where('message', 'LIKE', "%{$request->input('keyword')}%");
            },
        ])
            ->get()
            ->filter(function ($query) {
                return $query->with(['messages' => function ($q) {
                    $q->where('is_archive', true);
                }])->get();
            });
    }
}
