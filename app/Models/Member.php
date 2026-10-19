<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;   // Add this
use Illuminate\Foundation\Auth\User as Authenticatable; //  Extend from Authenticatable
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Branch;
use App\Models\MemberWallet;
use App\Support\SerialNumberGenerator;

class Member extends Authenticatable   //  Change from Model → Authenticatable
{
    use HasApiTokens, Notifiable, Branch;   // Add HasApiTokens

    protected $table = 'members';

    // protected $fillable = [
    //     'first_name', 'last_name', 'member_no', 'business_name', 'branch', 'email',
    //     'country_code', 'mobile', 'gender', 'city', 'state', 'zip', 'credit_source', 'address',
    //     'photo', 'pan_number', 'pan_image', 'aadhaar_number', 'aadhaar_front', 'aadhaar_back', 'password'
    // ];


     protected $fillable = [
        'first_name',
        'last_name',
        'member_no',
        'referral_code',
        'business_name',
        'branch_id',
        'user_id',
        'referred_by_user_id',
        'email',
        'country_code',
        'mobile',
        'gender',
        'city',
        'state',
        'zip',
        'credit_source',
        'address',
        'photo',
        'pancard_no',
        'pancard_image',
        'aadhaar_no',
        'aadhaar_front',
        'aadhaar_back',
        'password',
        'created_by',
        'assigned_user_id',
        'allow_withdrawal',
    ];


    protected $hidden = ['password', 'remember_token'];  //  Optional: hide password, token

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function booted()
    {
        static::creating(function (Member $member) {
            if (empty($member->referral_code)) {
                $member->referral_code = str_replace('-', '', SerialNumberGenerator::next('RB'));
            }
        });

        static::addGlobalScope('status', function (Builder $builder) {
            return $builder->where('status', 1);
        });

        static::created(function (Member $member) {
            $member->wallet()->firstOrCreate([
                'member_id' => $member->id,
            ], [
                'balance' => 0,
            ]);
        });
    }

    public function getCreatedAtAttribute($value)
    {
        $date_format = get_date_format();
        $time_format = get_time_format();
        return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // 🔗 Relationships
    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault();
    }

    public function loans()
    {
        return $this->hasMany('App\Models\Loan', 'borrower_id');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\MemberDocument', 'member_id');
    }

    public function fds()
    {
        return $this->hasMany(\App\Models\FD::class, 'customer_id');
    }

    public function investment()
    {
        return $this->hasMany(Investment::class, 'customer_id');
    }

    public function wallet()
    {
        return $this->hasOne(MemberWallet::class);
    }

    public function referrer()
    {
        return $this->belongsTo(self::class, 'referred_by_member_id')->withoutGlobalScopes();
    }

    public function referredByUser()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referrals()
    {
        return $this->hasMany(self::class, 'referred_by_member_id')->withoutGlobalScopes();
    }
}
