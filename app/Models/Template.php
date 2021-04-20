<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'trigger',
        'endpoint',
        'order',
        'template_id',
        'is_enabled',
        'user_id',
        'error_template_id',
    ];

    protected $guarded = [];
}
