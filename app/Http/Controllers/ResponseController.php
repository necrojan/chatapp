<?php

namespace App\Http\Controllers;

use App\CannedResponse;
use App\Http\Resources\CannedResponseResource;

class ResponseController extends Controller
{
    public function index()
    {
        $cannedResponse = CannedResponse::whereIsPersonal(false)
            ->orderBy('id', 'desc')->get();

        return CannedResponseResource::collection($cannedResponse);
    }
}