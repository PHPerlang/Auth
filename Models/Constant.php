<?php

namespace Modules\Auth\Models;

use Gindowin\Model;

class Constant extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_constants';


    /**
     * Indicates if the model has primary key.
     *
     * @var bool
     */
    public $primaryKey = 'const_key';


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
     * Set the const.
     *
     * @param $key
     * @param $value
     */
    public static function setValue($key, $value)
    {
        $const = new self;
        $const->const_key = $key;
        $const->const_value = $value;
        $const->save();
    }

    /**
     * Get the const.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $const = self::find($key);

        return $const ? $const->const_value : $default;
    }

    /**
     * Remove the const.
     *
     * @param $key
     *
     * @return mixed
     */
    public static function removeKey($key)
    {
        $const = self::find($key);

        if ($const) {
            $const->delete();
        }

    }

}