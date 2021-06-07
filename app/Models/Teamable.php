<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teamable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teamable()
    {
        return $this->morphTo();
    }

    /**
     * Get the action that belongs to owner team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function detail()
    {
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
    }
}
