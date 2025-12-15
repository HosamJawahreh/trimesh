<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $rules = PricingRule::orderBy('material')->orderBy('technology')->get();
        return view('admin.pricing.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material' => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'price_per_cm3' => 'required|numeric|min:0',
            'minimum_price' => 'required|numeric|min:0',
            'setup_fee' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $request->has('enabled');

        PricingRule::create($validated);

        return redirect()->back()->with('success', 'Pricing rule created successfully');
    }

    public function update(Request $request, PricingRule $pricing)
    {
        $validated = $request->validate([
            'material' => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'price_per_cm3' => 'required|numeric|min:0',
            'minimum_price' => 'required|numeric|min:0',
            'setup_fee' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'enabled' => 'boolean',
        ]);

        $validated['enabled'] = $request->has('enabled');

        $pricing->update($validated);

        return redirect()->back()->with('success', 'Pricing rule updated successfully');
    }

    public function destroy(PricingRule $pricing)
    {
        $pricing->delete();
        return redirect()->back()->with('success', 'Pricing rule deleted successfully');
    }
}
