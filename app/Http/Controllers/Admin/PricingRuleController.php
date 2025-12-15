<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    /**
     * Display pricing rules management page
     */
    public function index()
    {
        $pricingRules = PricingRule::orderBy('sort_order')->orderBy('display_name')->get();
        
        return view('admin.pricing.index', compact('pricingRules'));
    }

    /**
     * Get all pricing rules (API)
     */
    public function getAll()
    {
        $pricingRules = PricingRule::orderBy('sort_order')->orderBy('display_name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $pricingRules
        ]);
    }

    /**
     * Store a new pricing rule
     */
    public function store(Request $request)
    {
        $request->validate([
            'material' => 'required|string|max:255|unique:pricing_rules,material',
            'technology' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'price_per_cm3' => 'required|numeric|min:0',
            'price_per_mm2' => 'nullable|numeric|min:0',
            'minimum_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'multiplier' => 'required|numeric|min:0.1|max:10',
            'machine_hour_rate' => 'nullable|numeric|min:0',
            'print_speed_mm3_per_hour' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $pricingRule = PricingRule::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pricing rule created successfully',
                'data' => $pricingRule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pricing rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing pricing rule
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'material' => 'required|string|max:255|unique:pricing_rules,material,' . $id,
            'technology' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'price_per_cm3' => 'required|numeric|min:0',
            'price_per_mm2' => 'nullable|numeric|min:0',
            'minimum_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'multiplier' => 'required|numeric|min:0.1|max:10',
            'machine_hour_rate' => 'nullable|numeric|min:0',
            'print_speed_mm3_per_hour' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $pricingRule = PricingRule::findOrFail($id);
            $pricingRule->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pricing rule updated successfully',
                'data' => $pricingRule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a pricing rule
     */
    public function destroy($id)
    {
        try {
            $pricingRule = PricingRule::findOrFail($id);
            $pricingRule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pricing rule deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pricing rule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle pricing rule active status
     */
    public function toggleActive($id)
    {
        try {
            $pricingRule = PricingRule::findOrFail($id);
            $pricingRule->is_active = !$pricingRule->is_active;
            $pricingRule->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => ['is_active' => $pricingRule->is_active]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:pricing_rules,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        try {
            foreach ($request->items as $item) {
                PricingRule::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sort order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
