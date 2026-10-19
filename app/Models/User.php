<?php

namespace App\Models;

use App\Support\SerialNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'profile_picture',
        'two_factor_code',
        'two_factor_expires_at',
        'assigned_manager_id',
        'role_id',
        'branch_id',
        'mobile_number',
        'user_id',
        'referral_code',
        'percent',
        'addhaar_front',
        'addhaar_back',
        'designation',
        'salary',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $type = strtolower(trim((string) $user->user_type));
            // Admin permissions are controlled by user_type, not an office role.
            // Prevent a stale DSA/employee role from being attached to an admin.
            if ($type === 'admin') {
                $user->role_id = null;
                $user->referral_code = null;
                return;
            }

            // Client logins use the linked member's RB code; RBU codes identify
            // office users only.
            if ($type !== 'user') {
                $user->referral_code = null;
                return;
            }

            if (blank($user->referral_code)) {
                $user->referral_code = str_replace('-', '', SerialNumberGenerator::next('RBU'));
            }
        });

        static::creating(function (User $user) {
            if ($user->user_type !== 'user') {
                return;
            }

            if (blank($user->referral_code)) {
                $user->referral_code = str_replace('-', '', SerialNumberGenerator::next('RBU'));
            }

            if ($user->user_type !== 'user' || !blank($user->user_id)) {
                return;
            }

            $roleName = strtolower(trim((string) DB::table('roles')->where('id', $user->role_id)->value('name')));
            $prefix = str_contains($roleName, 'dsa')
                ? 'DSA'
                : ((str_contains($roleName, 'employee') || str_contains($roleName, 'manager')) ? 'EMP' : 'BUS');

            $user->user_id = SerialNumberGenerator::next($prefix);
        });
    }

    public function getCreatedAtAttribute($value)
    {
        $date_format = get_date_format();
        $time_format = get_time_format();
        return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id')->withDefault();
    }

    public function member()
    {
        return $this->hasOne('App\Models\Member', 'user_id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id')->withDefault();
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'created_user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'user_id');
    }

    public function assignedManager()
    {
        return $this
            ->belongsTo('App\Models\User', 'assigned_manager_id')
            ->whereHas('role', function ($query) {
                $query->where('name', 'manager');
            })
            ->withDefault();
    }

    public function assignedManagerDetails()
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function assignedAgents()
    {
        return $this
            ->hasMany('App\Models\User', 'assigned_manager_id')
            ->whereHas('role', function ($query) {
                $query->where('name', 'agent');
            });
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(30);
        $this->two_factor_code_count++;
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->two_factor_code_count = 0;
        $this->save();
    }

    /**
     * Get sidebar menu_keys this user is allowed to see.
     * Admin always sees everything (returns ['*']).
     * Others get their role's sidebar_permissions.
     */
    public function getSidebarPermissions(): array
    {
        // Admin bypass — sees all menus
        if ($this->user_type === 'admin') {
            return ['*'];
        }

        if (!$this->role_id) {
            return [];
        }

        $perms = \App\Models\SidebarPermission::getKeysForRole((int) $this->role_id);

        // Auto-include parent keys (e.g. if 'taxation.orders' exists, add 'taxation')
        $allPerms = $perms;
        foreach ($perms as $p) {
            if (str_contains($p, '.')) {
                $parent = explode('.', $p)[0];
                if (!in_array($parent, $allPerms)) {
                    $allPerms[] = $parent;
                }
            }
        }

        return $allPerms;
    }
}
