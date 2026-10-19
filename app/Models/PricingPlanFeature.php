<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'pricing_plan_id',
        'feature_name',
        'feature_status',
        'sort_order'
    ];

    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }
}
