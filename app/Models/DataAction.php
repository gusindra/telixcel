<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'action_id',
    ];

    protected $guarded = [];

    /**
     * Get the action that belongs to template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }
}
