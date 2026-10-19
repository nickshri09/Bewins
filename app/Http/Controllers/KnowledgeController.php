<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\Knowledge;
use Illuminate\Support\Collection;

class KnowledgeController extends Controller
{
    public function index()
    {
        // Plans
        $pricingPlans = PricingPlan::with('features')
            ->active()
            ->ordered()
            ->get();

        // Knowledge grouped by category and only active
        $knowledge = Knowledge::with('images')
            ->where('is_active', true)
            ->latest()
            ->get();

        // Group by category with keys: home, service, about
        /** @var array<string, Collection> $knowledgeByCategory */
        $knowledgeByCategory = [
            'home'    => $knowledge->where('category', 'home')->values(),
            'service' => $knowledge->where('category', 'service')->values(),
            'about'   => $knowledge->where('category', 'about')->values(),
        ];

        return view('knowledge.index', compact('pricingPlans', 'knowledgeByCategory'));
    }

    public function showPlan(PricingPlan $plan)
    {
        $plan->load('features');
        return view('knowledge.plan', compact('plan'));
    }
}

