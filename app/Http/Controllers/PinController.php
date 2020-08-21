<?php

namespace App\Http\Controllers;

use App\Pin;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PinController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(auth()->user()->pins);
    }
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required|string|max:255'
        ]);

        auth()->user()->pins()->updateOrCreate([
            'text' => $request->input('text')
        ]);

        return response()->json('pin added');
    }

    /**
     * @param Pin $pin
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function destroy(Pin $pin)
    {
        $pin->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
