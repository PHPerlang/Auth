<?php

namespace Modules\Core\Models;


class Constant extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'constants';


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
    public static function set($key, $value)
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
    public static function get($key, $default = null)
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
    public static function remove($key)
    {
        $const = self::find($key);

        if ($const) {
            $const->delete();
        }

    }

}