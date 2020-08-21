<?php

namespace App\Policies;

use App\CannedResponse;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CannedResponsePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create canned responses.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole(['admin', 'agent']);
    }

    /**
     * Determine whether the user can update the canned response.
     *
     * @param User $user
     *
     * @param CannedResponse $response
     * @return mixed
     */
    public function update(User $user, CannedResponse $response)
    {
        return $user->hasRole(['admin', 'agent']) && $user->id === $response->user_id;
    }

    /**
     * Determine whether the user can delete the canned response.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasRole('admin');
    }
}
