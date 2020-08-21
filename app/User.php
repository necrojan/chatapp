<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'photo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return HasOne
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * @return HasMany
     */
    public function response()
    {
        return $this->hasMany(CannedResponse::class);
    }

    /**
     * @return HasMany
     */
    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    /**
     * @param $roleName string
     *
     * @return User
     */
    public function assignRoleTitle($roleName)
    {
        Role::findOrCreate($roleName);

        return $this->assignRole($roleName);
    }
}
