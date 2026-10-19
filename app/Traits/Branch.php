<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Branch
{
    public static function bootBranch()
    {
        static::addGlobalScope('branch_id', function (Builder $builder) {
            // Only use the web guard here. Calling the sanctum guard from a
            // model global scope causes recursive authentication when Sanctum
            // tries to resolve the tokenable model itself.
            $user = Auth::guard('web')->user();

            if ($user && $user->user_type == 'user') {
                // For normal user
                return $builder->where('branch_id', $user->branch_id);
            } elseif (session()->has('branch_id') && session('branch_id') != '') {
                // For admin using branch filter
                $branch_id = session('branch_id') === 'default' ? null : session('branch_id');
                return $builder->where('branch_id', $branch_id);
            }
            // If no auth user or branch session, do nothing (no filter)
        });
    }
}
