<?php

namespace App\Http\Controllers;

use App\Client;
use App\Events\AcceptedBy;
use App\Events\RemovePool;
use App\Events\RestorePool;
use App\Pool;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PoolController extends Controller
{
    /**
     * @return Collection
     */
    public function index()
    {
        $clientIds = Pool::query()->pluck('client_id')->all();

        return Client::query()
            ->with('user')
            ->whereIn('id', $clientIds)
            ->get();
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function store($id)
    {
        try {
            $pool = Pool::withTrashed()
                ->where('client_id', $id)->firstOrFail();

            $pool->restore();

            $pool->client()->update([
                'accepted_by' => null,
                'is_verified' => false,
            ]);

            event(new RestorePool($pool->client->load('user')));

            return response()->json('restored');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param         $id
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $userId = $request->get('userId');
        $user = User::find($userId);

        try {
            $pool = Pool::where('client_id', $id)->firstOrFail();

            $pool->delete();

            $pool->client()->update(['accepted_by' => $userId]);

            event(new RemovePool($pool->client->load('user'), $userId));

            event(new AcceptedBy($user, $pool->client));

            return response()->json([
                'accepted_by' => $user->name,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}