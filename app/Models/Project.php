<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'type',
        'status',
        'entity_party',
        'customer_name',
        'customer_address',
        'contact_id',
        'referrer_name',
        'team_id',
        'product_line',
        'user_id',
    ];

    public static $searchable=[
        "name"
    ];

    protected $guarded = [];

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        if (Str::isUuid((string) $value)) {
            return $this->where('uuid', $value)->firstOrFail();
        }

        if (ctype_digit((string) $value)) {
            return $this->whereKey($value)->firstOrFail();
        }

        return $this->where('uuid', $value)->firstOrFail();
    }

    /**
     * Get all of customer.
     */
    public function customer(){
    	return $this->belongsTo('App\Models\Client');
    }

    /**
     * Get all of team.
     */
    public function team(){
    	return $this->belongsTo('App\Models\Team');
    }

    /**
     * Get the action that belongs to product line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productLine()
    {
        return $this->belongsTo('App\Models\ProductLine', 'product_line');
    }

    /**
     * Get all of team.
     */
    public function approval(){
    	return $this->hasOne('App\Models\FlowProcess', 'model_id')->where('model', 'PROJECT')->whereNull('status');
    }

    /**
     * Get all contract of project.
     */
    public function contracts(){
        return $this->hasMany('App\Models\Contract', 'model_id')->where('model', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get all contract of project.
     */
    public function orders(){
        return $this->hasMany('App\Models\Order', 'source_id')->where('source', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get all contract of project.
     */
    public function quotations(){
        return $this->hasMany('App\Models\Quotation', 'model_id')->where('model', 'PROJECT')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get company owner
     *
     * @return void
     */
    public function company(){
    	return $this->belongsTo('App\Models\Company', 'entity_party');
    }

    /**
     * Clients attached to this project (1 project — many clients).
     */
    public function clients(){
    	return $this->belongsToMany('App\Models\Client', 'project_client')->withTimestamps();
    }

    /**
     * Users assigned to this project (assign only — does not create users).
     */
    public function members(){
    	return $this->belongsToMany('App\Models\User', 'project_user')->withPivot('role')->withTimestamps();
    }

    /**
     * To-do tasks of this project.
     */
    public function tasks(){
    	return $this->hasMany('App\Models\Task', 'project_id')->orderBy('created_at', 'desc');
    }
}
