<?php

namespace App\Http\Controllers;

use App\Client;

class AcceptedClientController
{
    public function index()
    {
        return Client::query()
            ->acceptedBy()
            ->get();
    }

    public function show()
    {
        return response()->json(auth()->user()->client->accepted_by);
    }
}