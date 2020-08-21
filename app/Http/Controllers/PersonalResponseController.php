<?php

namespace App\Http\Controllers;

use App\CannedResponse;
use App\Types\CannedResponseType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PersonalResponseController extends Controller
{
    public function index()
    {
        $cannedResponse = CannedResponse::whereIsPersonal(true)
            ->whereUserId(auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(CannedResponseType::PAGINATION_COUNT);

        return view('response.personal.index', [
            'responses' => $cannedResponse,
        ]);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        abort_unless(auth()->user()->hasRole(['admin', 'agent']), 403);

        return view('response.personal.create');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|unique:canned_responses|max:255',
            'message' => 'required|max:255',
        ]);

        auth()->user()->response()->create([
            'key' => $request->input('key'),
            'message' => $request->input('message'),
            'is_personal' => true
        ]);

        session()->flash('create_personal_response', 'Successfully created a personal response!');

        return redirect(route('responses.personal.index'));
    }

    /**
     * @param CannedResponse $response
     *
     * @return Factory|View
     */
    public function edit(CannedResponse $response)
    {
        abort_unless(auth()->user()->hasRole(['admin', 'agent']), 403);

        return view('response.personal.edit', compact('response'));
    }

    /**
     * @param Request        $request
     * @param CannedResponse $response
     *
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function update(Request $request, CannedResponse $response)
    {
        $this->validate($request, [
            'key' => 'required|max:255|unique:canned_responses,key,'.$response->id,
            'message' => 'required|max:255',
        ]);

        $response->update([
            'key' => $request->input('key'),
            'message' => $request->input('message'),
            'is_personal' => true,
        ]);

        session()->flash('update_personal_response', 'Successfully updated a personal response!');

        return redirect(route('responses.personal.edit', $response));
    }

    /**
     * @param CannedResponse $response
     *
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function destroy(CannedResponse $response)
    {
        $response->delete();

        session()->flash('delete_personal_response', 'Successfully deleted a personal response!');

        return redirect(route('responses.personal.index'));
    }
}