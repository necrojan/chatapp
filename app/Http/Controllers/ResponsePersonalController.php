<?php

namespace App\Http\Controllers;

use App\CannedResponse;
use Illuminate\Http\JsonResponse;

class ResponsePersonalController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $cannedResponse = CannedResponse::whereIsPersonal(true)
            ->whereUserId(auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($cannedResponse);
    }
}
