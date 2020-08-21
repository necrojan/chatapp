<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CannedResponse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'message', 'is_personal'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_personal' => 'boolean'
    ];

    /**
     * @return string
     */
    public function getRouteAttribute()
    {
        return route('responses.edit', $this);
    }
}
