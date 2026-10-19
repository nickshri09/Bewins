<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Member {

    protected static function bootMember() {

        static::addGlobalScope('member_id', function (Builder $builder) {

            // 🔥 API requests → No scope apply
            if (request()->is('api/*') || request()->segment(1) === 'api') {
                return;
            }

            // 🔐 Safe user fetch
            $user = auth()->user();

            // ❌ No user → no filter
            if (!$user) {
                return;
            }

            // 👤 Normal user (user panel)
            if ($user->user_type === 'user') {

                return $builder->whereHas('member', function (Builder $query) use ($user) {
                    $query->where('branch_id', $user->branch_id);
                });
            }

            // 🛠 Admin + Branch filter
            if (session()->has('branch_id') && session('branch_id') !== '') {

                $branch_id = session('branch_id') === 'default'
                    ? null
                    : session('branch_id');

                return $builder->whereHas('member', function (Builder $query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
            }

        });

    }

}
