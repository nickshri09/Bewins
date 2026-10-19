<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarPermission extends Model
{
    protected $table = 'sidebar_permissions';

    protected $fillable = ['role_id', 'menu_key'];

    /**
     * Get all menu_keys granted to a given role_id.
     */
    public static function getKeysForRole(int $roleId): array
    {
        return self::where('role_id', $roleId)->pluck('menu_key')->toArray();
    }
}
