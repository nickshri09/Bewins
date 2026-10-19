<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Support\SerialNumberGenerator;

class Investment extends Model
{
    use HasFactory;

    protected $table='investments';
    protected $fillable = [
        'customer_id',
        'investment_amount',
        'investment_type',
        'risk_level',
        'expected_return',
        'tds_percent',
        'start_date',
        'maturity_date',
        'current_value',
        'dividends',
        'capital_gains',
        'status',
        'notes',
        'currency_id',
        'description',
        'remarks',
        'contribution_type',
        'created_user_id',
        'disbused',
        'maturity_amount'
    ];

protected $casts = [
    'start_date' => 'datetime',
    'maturity_date' => 'datetime',
    'closed_at' => 'datetime',
    'closure_certificate_sent_at' => 'datetime',
    'agreement_email_sent_at' => 'datetime',
];

    protected static function booted(): void
    {
        static::creating(function (Investment $investment) {
            if (blank($investment->investment_code)) {
                $investment->investment_code = SerialNumberGenerator::next('BCS');
            }
        });
    }

public function getStatusLabelAttribute()
{
    return match($this->status) {
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
        3 => 'Completed',
        4 => 'Withdrawn',
        default => 'Unknown',
    };
}

    // Relations
    public function customer()
    {
        return $this->belongsTo(Member::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    public function member(){
    return $this->belongsTo(Member::class,'customer_id');
}

public function created_by() {
    return $this->belongsTo('App\Models\User', 'created_user_id')->withDefault();
}


public function agreement_documents()
{
    return $this->hasMany(InvestmentAgreementDocument::class);
}

public function wallet_transactions()
{
    return $this->hasMany(MemberWalletTransaction::class, 'investment_id');
}

    // Update returns
    public function updateReturns()
    {
        $today = Carbon::today();

        switch($this->investment_type) {
            case 'mutual_fund':
                $days = Carbon::parse($this->start_date)->diffInDays($today);
                $rate = $this->expected_return / 100;
                $years = $days / 365;
                $current_value = $this->investment_amount * pow((1 + $rate), $years);
                $dividends = ($current_value - $this->investment_amount) * 0.2;
                $capital_gains = $current_value - $this->investment_amount - $dividends;
                break;

            default:
                $current_value = $this->investment_amount;
                $dividends = 0;
                $capital_gains = 0;
        }

        $this->update([
            'current_value' => round($current_value,2),
            'dividends' => round($dividends,2),
            'capital_gains' => round($capital_gains,2)
        ]);

        if($this->maturity_date && $today->gte(Carbon::parse($this->maturity_date))) {
           $this->update(['status' => 3]); // Completed

        }
    }
}
