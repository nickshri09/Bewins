<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Support\SerialNumberGenerator;

class Loan extends Model {

    protected $casts = [
        'agreement_email_sent_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';

    // old
    // protected static function booted() {
    //     static::addGlobalScope('borrower_id', function (Builder $builder) {
    //         if (auth()->user()->user_type == 'user') {
    //             return $builder->whereHas('borrower', function (Builder $query) {
    //                 $query->where('branch_id', auth()->user()->branch_id);
    //             });
    //         } else {
    //             if (session('branch_id') != '') {
    //                 $branch_id = session('branch_id') == 'default' ? null : session('branch_id');
    //                 return $builder->whereHas('borrower', function (Builder $query) use ($branch_id) {
    //                     $query->where('branch_id', $branch_id);
    //                 });
    //             }
    //         }
    //     });
    // }

    // new
    // protected static function booted() {
    //     static::addGlobalScope('borrower_id', function (Builder $builder) {

    //         // 🔥 auth null होने से बचाने के लिए check जरूरी है
    //         if (auth()->check() && auth()->user()->user_type == 'user') {

    //             return $builder->whereHas('borrower', function (Builder $query) {
    //                 $query->where('branch_id', auth()->user()->branch_id);
    //             });

    //         } else {

    //             if (session('branch_id') != '') {
    //                 $branch_id = session('branch_id') == 'default' ? null : session('branch_id');
    //                 return $builder->whereHas('borrower', function (Builder $query) use ($branch_id) {
    //                     $query->where('branch_id', $branch_id);
    //                 });
    //             }
    //         }
    //     });
    // }

    protected static function booted() {
        static::creating(function (Loan $loan) {
            if (blank($loan->loan_id)) {
                $loan->loan_id = SerialNumberGenerator::next('BLN');
            }
        });

        static::addGlobalScope('borrower_id', function (Builder $builder) {

            // Disable ALL global scopes for API requests
            if (request()->is('api/*') || request()->segment(1) === 'api') {
                return;
            }

            // WEB USER
            if (auth()->check() && auth()->user() && auth()->user()->user_type == 'user') {
                return $builder->whereHas('borrower', function (Builder $query) {
                    $query->where('branch_id', auth()->user()->branch_id);
                });
            }

            // WEB ADMIN with SESSION filter
            if (session('branch_id') != '') {
                $branch_id = session('branch_id') == 'default' ? null : session('branch_id');
                return $builder->whereHas('borrower', function (Builder $query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
            }
        });
    }




    public function borrower() {
        return $this->belongsTo('App\Models\Member', 'borrower_id')->withDefault();
    }

    public function currency() {
        return $this->belongsTo('App\Models\Currency', 'currency_id')->withDefault();
    }

    public function loan_product() {
        return $this->belongsTo('App\Models\LoanProduct', 'loan_product_id')->withDefault();
    }

    public function approved_by() {
        return $this->belongsTo('App\Models\User', 'approved_user_id')->withDefault();
    }

    public function created_by() {
        return $this->belongsTo('App\Models\User', 'created_user_id')->withDefault();
    }

    public function collaterals() {
        return $this->hasMany('App\Models\LoanCollateral', 'loan_id');
    }

    public function guarantors() {
        return $this->hasMany('App\Models\Guarantor', 'loan_id');
    }

    public function repayments() {
        return $this->hasMany('App\Models\LoanRepayment', 'loan_id');
    }

    public function payments() {
        return $this->hasMany('App\Models\LoanPayment', 'loan_id');
    }

    public function next_payment() {
        return $this->hasOne('App\Models\LoanRepayment', 'loan_id')
            ->where('status', 0)->orderBy('id', 'asc')
            ->withDefault();
    }

    public function agreement_documents()
    {
        return $this->hasMany(LoanAgreementDocument::class);
    }
    //old
    // public function getFirstPaymentDateAttribute($value) {
    //     $date_format = get_date_format();
    //     return \Carbon\Carbon::parse($value)->format("$date_format");
    // }

    // public function getReleaseDateAttribute($value) {
    //     if ($value != null) {
    //         $date_format = get_date_format();
    //         return \Carbon\Carbon::parse($value)->format("$date_format");
    //     }
    // }

    // public function getApprovedDateAttribute($value) {
    //     if ($value != null) {
    //         $date_format = get_date_format();
    //         return \Carbon\Carbon::parse($value)->format("$date_format");
    //     }
    // }

    // public function getCreatedAtAttribute($value) {
    //     $date_format = get_date_format();
    //     $time_format = get_time_format();
    //     return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    // }

    // public function getUpdatedAtAttribute($value) {
    //     $date_format = get_date_format();
    //     $time_format = get_time_format();
    //     return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    // }


    //new
    public function getFirstPaymentDateAttribute($value) {
        if (empty($value)) return null;   // <-- FIX
        return \Carbon\Carbon::parse($value)->format(get_date_format());
    }

    public function getReleaseDateAttribute($value) {
        if (empty($value)) return null;
        return \Carbon\Carbon::parse($value)->format(get_date_format());
    }

    public function getApprovedDateAttribute($value) {
        if (empty($value)) return null;
        return \Carbon\Carbon::parse($value)->format(get_date_format());
    }

    public function getCreatedAtAttribute($value) {
        if (empty($value)) return null;
        return \Carbon\Carbon::parse($value)->format(get_date_format().' '.get_time_format());
    }

    public function getUpdatedAtAttribute($value) {
        if (empty($value)) return null;
        return \Carbon\Carbon::parse($value)->format(get_date_format().' '.get_time_format());
    }


}
