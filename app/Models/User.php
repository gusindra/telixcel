<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'handling', 'phone_no', 'nick'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function clients($m=null, $y=null){
        if($m && $y){
            return $this->hasMany('App\Models\Client', 'user_id')->whereMonth('created_at', '<=', $m)->whereYear('created_at', '<=', $y);
        }
    	return $this->hasMany('App\Models\Client', 'user_id');
    }
    public function inbounds($m=null, $y=null){
        if($m && $y){
            return $this->hasMany('App\Models\Request', 'user_id')->whereNotNull('sent_at')->whereMonth('created_at', $m)->whereYear('created_at', $y);
        }
    	return $this->hasMany('App\Models\Request', 'user_id')->whereNotNull('sent_at');
    }
    public function outbounds($m=null, $y=null){
        if($m && $y){
            return $this->hasMany('App\Models\Request', 'user_id')->whereNull('sent_at')->whereMonth('created_at', $m)->whereYear('created_at', $y);
        }
    	return $this->hasMany('App\Models\Request', 'user_id')->whereNull('sent_at');
    }

    /**
     * User has one Chat Session
     *
     * @return void
     */
    public function chatsession(){
    	return $this->hasOne('App\Models\HandlingSession', 'agent_id');
    }

    /**
     * User Has many Team
     *
     * @return void
     */
    public function super(){
    	return $this->hasMany('App\Models\TeamUser','user_id')->where('team_id', 1);
    }

    /**
     * User is Superuser
     *
     * @return void
     */
    public function isSuper(){
    	return $this->hasOne('App\Models\TeamUser','user_id')->where('team_id', 1);
    }

    /**
     * teams
     *
     * @return void
     */
    public function teams(){
    	return $this->hasMany('App\Models\Team', 'user_id');
    }

    /**
     * list teams
     *
     * @return void
     */
    public function listTeams(){
    	return $this->hasMany('App\Models\TeamUser', 'user_id');
    }

    /**
     * billings
     *
     * @return void
     */
    public function billings(){
    	return $this->hasMany('App\Models\Billing', 'user_id');
    }

    /**
     * User Has many Role
     *
     * @return void
     */
    public function role(){
    	return $this->hasMany('App\Models\RoleUser','user_id');
    }
}
