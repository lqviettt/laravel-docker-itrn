<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\Models\ProductVariant;
use Modules\Product\Models\VariantOption;

class ProductVariantController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $pvariant = ProductVariant::query();

        return response()->json($pvariant->paginate($perPage));
    }

    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'variant_option_id' => 'required|exists:variant_options,id',
            'value' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        $variantOption = VariantOption::findOrFail($request->variant_option_id);

        if (!$variantOption) {
            return response()->json(['error' => 'Variant option not found'], 400);
        }

        if ($variantOption->type == 'color' && $request->value != $variantOption->name) {
            return response()->json(['error' => 'Invalid color value. Expected: ' . $variantOption->name], 400);
        }

        if ($variantOption->type == 'storage' && $request->value != $variantOption->name) {
            return response()->json(['error' => 'Invalid storage value. Expected: ' . $variantOption->name], 400);
        }

        $variant = ProductVariant::create([
            'product_id' => $productId,
            'variant_option_id' => $validated['variant_option_id'],
            'value' => $validated['value'],
            'price' => $validated['price'],
        ]);

        return response()->json($variant, 201);
    }

    public function show($pvariantId)
    {
        $pvariant = ProductVariant::with('product')->findOrFail($pvariantId);

        return response()->json($pvariant);
    }

    public function update(Request $request, $pvariantId)
    {
        $validated = $request->validate([
            'variant_option_id' => 'sometimes|exists:variant_options,id',
            'value' => 'sometimes|string|max:50',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $variant = ProductVariant::findOrFail($pvariantId);

        $variant->update($validated);

        return response()->json($variant);
    }

    public function delete($pvariantId)
    {
        $variant = ProductVariant::findOrFail($pvariantId);

        $variant->delete();

        return response()->json(['message' => 'Variant deleted successfully']);
    }

    public function addVariant(Request $request)
    {
        $validated = $request->validate([
            "type" => "required|string|max:50",
            "name" => "required|string|max:50",
        ]);

        $variant = VariantOption::create([
            "type" => $validated['type'],
            "name" => $validated['name']
        ]);

        return response()->json($variant);
    }

    public function getVariant(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $pvariant = VariantOption::query();

        return response()->json($pvariant->paginate($perPage));
    }
}
