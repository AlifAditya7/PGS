<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:consulting,auditing,training',
            'type' => 'required|in:online,offline',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'activities' => 'nullable|array',
        ]);

        $data['slug'] = Str::slug($data['name']);
        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:consulting,auditing,training',
            'type' => 'required|in:online,offline',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'activities' => 'nullable|array',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diupdate.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}