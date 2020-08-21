<?php

namespace App\Http\Controllers;

use App\Client;
use App\Pool;
use Illuminate\Support\Collection;

class ClientController extends Controller
{
    /**
     * @return Collection
     */
    public function index()
    {
        $clientIds = Pool::query()->pluck('client_id')->all();

        return Client::query()
            ->with('user')
            ->whereNotIn('id', $clientIds)->get();
    }
}