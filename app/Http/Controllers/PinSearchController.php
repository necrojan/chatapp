<?php

namespace App\Http\Controllers;

use App\CannedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PinSearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $searchResponse = CannedResponse::query()
            ->where(function ($query) use ($request) {
                $query->where('is_personal', true)
                    ->where('user_id', auth()->id())
                ->where('message', 'LIKE', "%{$request->input('search')}%");
            })
            ->orWhere(function ($query) {
                $query->where('is_personal', false);
            })
            ->where('message', 'LIKE', "%{$request->input('search')}%")
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($searchResponse);
    }
}