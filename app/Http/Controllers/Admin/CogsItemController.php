<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CogsItem;
use Illuminate\Http\Request;

class CogsItemController extends Controller
{
    public function index()
    {
        $items = CogsItem::latest()->get();
        return view('admin.cogs-items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'price' => 'required|numeric']);
        CogsItem::create($request->all());
        return redirect()->back()->with('success', 'Item COGS berhasil ditambah.');
    }

    public function update(Request $request, CogsItem $cogsItem)
    {
        $request->validate(['name' => 'required|string', 'price' => 'required|numeric']);
        $cogsItem->update($request->all());
        return redirect()->back()->with('success', 'Item COGS berhasil diupdate.');
    }

    public function destroy(CogsItem $cogsItem)
    {
        $cogsItem->delete();
        return redirect()->back()->with('success', 'Item COGS berhasil dihapus.');
    }
}