<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeItem;
use Illuminate\Http\Request;

class FeeItemController extends Controller
{
    public function index()
    {
        $items = FeeItem::all();
        return view('admin.finance.fee_items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fee_items,name',
            'default_description' => 'nullable|string',
        ]);

        FeeItem::create($validated);

        return redirect()->back()->with('success', 'Fee Item created successfully.');
    }

    public function update(Request $request, FeeItem $feeItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fee_items,name,' . $feeItem->id,
            'default_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $feeItem->update($validated);

        return redirect()->back()->with('success', 'Fee Item updated successfully.');
    }
}
