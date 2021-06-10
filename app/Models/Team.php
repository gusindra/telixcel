<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Jetstream\HasProfilePhoto;

class Team extends JetstreamTeam
{
    use HasFactory;
    use HasProfilePhoto;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get all of the templates that are assigned this team.
     */
    public function template()
    {
        return $this->morphedByMany(Template::class, 'teamable');
    }

    /**
     * Get all of the api that are assigned this team.
     */
    public function apiCredential()
    {
        return $this->morphedByMany(ApiCredential::class, 'teamable');
    }

    /**
     * Get all of the request that are assigned this team.
     */
    public function requestAll()
    {
        return $this->morphedByMany(Request::class, 'teamable');
    }

    /**
     * Get all of the request that are assigned this team.
     */
    public function requestIn()
    {
        return $this->morphedByMany(Request::class, 'teamable')->inbounce();
    }

    /**
     * Get all of the request that are assigned this team.
     */
    public function requestOut()
    {
        return $this->morphedByMany(Request::class, 'teamable')->outbounce();
    }

    /**
     * Get all of the client that are assigned this team.
     */
    public function client()
    {
        return $this->morphedByMany(Client::class, 'teamable');
    }

    /**
     * Get agent team that are assigned this team.
     */
    public function agents()
    {
        return $this->hasMany(TeamUser::class, 'team_id');
    }
}
