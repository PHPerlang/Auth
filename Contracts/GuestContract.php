<?php

namespace Modules\Core\Contracts;

interface GuestContract
{

    /**
     * Get the guest member id.
     *
     * @return int
     */
    public static function id();

    /**
     * Get the guest member instance
     *
     * @return \Modules\Core\Models\Member
     */
    public static function instance();

    /**
     * Check the guest permissions in frontend templates.
     *
     * Notice: this method is used in frontend templates to determine some view components
     * visibility, not used in api controllers.
     *
     * @return bool
     */
    public static function can();
}