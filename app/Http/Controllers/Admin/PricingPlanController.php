<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\PricingPlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PricingPlanController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::with('features')->ordered()->get();
        return view('backend.pricing_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('backend.pricing_plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'validity' => 'required|string|max:255',
            'cashback_description' => 'required|string',
            'icon_class' => 'required|string|max:255',
            'icon_color' => 'required|string|max:255',
            'features' => 'required|array',
            'features.*.feature_name' => 'required|string|max:255',
            'features.*.feature_status' => 'required|string|in:paid,yes,no,email',
        ]);

        DB::transaction(function () use ($request) {
            $plan = PricingPlan::create([
                'name' => $request->name,
                'price' => $request->price,
                'validity' => $request->validity,
                'cashback_description' => $request->cashback_description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
                'sort_order' => PricingPlan::max('sort_order') + 1,
            ]);

            foreach ($request->features as $index => $feature) {
                $plan->features()->create([
                    'feature_name' => $feature['feature_name'],
                    'feature_status' => $feature['feature_status'],
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.pricing-plans.index')
            ->with('success', 'Pricing plan created successfully!');
    }

    public function edit(PricingPlan $pricingPlan)
    {
        $plan = $pricingPlan->load('features');
        return view('backend.pricing_plans.edit', compact('plan'));
    }

    public function update(Request $request, PricingPlan $pricingPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'validity' => 'required|string|max:255',
            'cashback_description' => 'required|string',
            'icon_class' => 'required|string|max:255',
            'icon_color' => 'required|string|max:255',
            'features' => 'required|array',
            'features.*.feature_name' => 'required|string|max:255',
            'features.*.feature_status' => 'required|string|in:paid,yes,no,email',
        ]);

        DB::transaction(function () use ($request, $pricingPlan) {
            $pricingPlan->update([
                'name' => $request->name,
                'price' => $request->price,
                'validity' => $request->validity,
                'cashback_description' => $request->cashback_description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
            ]);

            // Delete existing features and create new ones
            $pricingPlan->features()->delete();

            foreach ($request->features as $index => $feature) {
                $pricingPlan->features()->create([
                    'feature_name' => $feature['feature_name'],
                    'feature_status' => $feature['feature_status'],
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.pricing-plans.index')
            ->with('success', 'Pricing plan updated successfully!');
    }

    public function destroy(PricingPlan $pricingPlan)
    {
        $pricingPlan->delete();
        return redirect()->route('admin.pricing-plans.index')
            ->with('success', 'Pricing plan deleted successfully!');
    }

    public function toggleStatus(PricingPlan $pricingPlan)
    {
        $pricingPlan->update(['is_active' => !$pricingPlan->is_active]);
        return redirect()->route('admin.pricing-plans.index')
            ->with('success', 'Pricing plan status updated successfully!');
    }
}
