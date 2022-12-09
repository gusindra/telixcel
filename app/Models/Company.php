<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'tax_id',
        'post_code',
        'province',
        'city',
        'address',
        'logo',
        'person_in_charge',
        'user_id',
    ];

	protected $table = 'companies';

    protected $guarded = [];

    /**
     * Get the flow that belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * attachment order
     *
     * @return void
     */
    public function img_logo(){
        return $this->hasOne('App\Models\Attachment', 'model_id')->where('model', 'company');
    }

    /**
     * Get the action that Client has manny Request .
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payable(){
    	return $this->hasMany(CompanyPayment::class, 'company_id');
    }

    /**
     * Get the action that Client has manny Request .
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projects(){
    	return $this->hasMany(Project::class, 'party_b');
    }
}
