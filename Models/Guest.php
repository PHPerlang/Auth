<?php

namespace Modules\Core\Models;

use Modules\Core\Contracts\GuestContract;

class Guest extends Member implements GuestContract
{
    /**
     * The guest member id.
     *
     * @var integer
     */
    protected static $id;

    /**
     * Set guest id to the static attribute.
     *
     * @param $guest_id
     */
    public static function init($guest_id)
    {
        self::$id = $guest_id;
    }

    /**
     * Get the guest member id.
     *
     * @return int
     */
    public static function id()
    {
        return self::$id;
    }

    /**
     * Get the guest member instance
     *
     * @return Member
     */
    public static function instance()
    {
        return self::find(self::$id);
    }

    /**
     * Check the guest permissions in frontend templates.
     *
     * Notice: this method is used in frontend templates to determine some view components
     * visibility, not used in api controllers.
     *
     * @return bool
     */
    public static function can()
    {
        return true;
    }

}