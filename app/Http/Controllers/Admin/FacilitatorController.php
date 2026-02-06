<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facilitator;
use Illuminate\Http\Request;

class FacilitatorController extends Controller
{
    public function index()
    {
        $facilitators = Facilitator::latest()->get();
        return view('admin.facilitators.index', compact('facilitators'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'price' => 'required|numeric']);
        Facilitator::create($request->all());
        return redirect()->back()->with('success', 'Facilitator berhasil ditambah.');
    }

    public function update(Request $request, Facilitator $facilitator)
    {
        $request->validate(['name' => 'required|string', 'price' => 'required|numeric']);
        $facilitator->update($request->all());
        return redirect()->back()->with('success', 'Facilitator berhasil diupdate.');
    }

    public function destroy(Facilitator $facilitator)
    {
        $facilitator->delete();
        return redirect()->back()->with('success', 'Facilitator berhasil dihapus.');
    }
}