<?php

namespace Modules\Auth\Models;

class Member extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'member_id';


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
        'member_role_id',
        'member_account',
        'member_email',
        'member_phone',
        'member_status',
        'member_avatar',
        'member_nickname',
        'member_password',
        'created_at',
        'updated_at',
    ];


    /**
     * Encrypt member password.
     *
     * @param string $value
     *
     * @return string
     */
    public function encryptMemberPassword($value)
    {
        return md5($value);
    }

    /**
     * Set member password.
     *
     * @param  string $value
     */
    public function setMemberPasswordAttribute($value)
    {
        $this->attributes['member_password'] = $this->encryptMemberPassword($value);
    }

}