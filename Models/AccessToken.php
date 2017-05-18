<?php

namespace Modules\Core\Models;


class AccessToken extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'access_tokens';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = ['member_id', 'client_group'];


    /**
     * Indicates if the model primary auto incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'access_token',
        'client_group',
        'client_id',
        'client_version',
        'expired_at',
    ];

    /**
     * Set the access token attribute.
     *
     * @param  string $value
     * @return void
     */
    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = md5($value . time());
    }

}