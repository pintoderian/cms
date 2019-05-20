<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{

    public static function defaultPermissions()
    {
        return [
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'add_roles',
            'edit_roles',
            'delete_roles',

            'view_catalog',
            'add_catalog',
            'edit_catalog',
            'delete_catalog',

            'view_catalog_details',
            'add_catalog_details',
            'edit_catalog_details',
            'delete_catalog_details',

            'access_panel', //Permite el acceso al panel de administración,
            /*
             * Logs
             * */
            'logs_user_access',
            'logs_user_activity',
            'logs_system',
            'logs_system_view',
            /*
             * Settings
             * */
            'settings_catalogs',
        ];
    }
}
