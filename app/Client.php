<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'machine', 'code', 'is_verified', 'company', 'phone'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'machine' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope query to include accepted by clients.
     *
     * @param $query
     * @return mixed
     */
    public function scopeAcceptedBy($query)
    {
        return $query->where('accepted_by', '!=', null);
    }
}
