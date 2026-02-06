<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => Setting::get('company_name', 'PT. PGS Consulting Indonesia'),
            'company_address' => Setting::get('company_address', 'Jl. Contoh Alamat No. 123, Jakarta'),
            'company_phone' => Setting::get('company_phone', '021-12345678'),
            'company_whatsapp' => Setting::get('company_whatsapp', '08123456789'),
            'company_email' => Setting::get('company_email', 'info@pgs-consulting.com'),
            'bank_name' => Setting::get('bank_name', 'Bank Mandiri'),
            'bank_account_number' => Setting::get('bank_account_number', '123-456-7890'),
            'bank_account_name' => Setting::get('bank_account_name', 'PT. PGS Consulting Indonesia'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Profil Perusahaan berhasil diperbarui.');
    }
}