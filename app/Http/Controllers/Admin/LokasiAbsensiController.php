<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class LokasiAbsensiController extends Controller
{
    public function index()
    {
        $latitude  = (float) Pengaturan::where('key', 'latitude_sekolah')->value('value') ?? '-6.782073';
        $longitude = (float) Pengaturan::where('key', 'longitude_sekolah')->value('value') ?? '106.731083';
        $radius    = (float) Pengaturan::where('key', 'radius_meter')->value('value') ?? '200';

        return view('admin.lokasi.index', compact('latitude', 'longitude', 'radius'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'latitude_sekolah'  => 'required|numeric',
            'longitude_sekolah' => 'required|numeric',
            'radius_meter'      => 'required|numeric|min:10',
        ]);

        Pengaturan::updateOrCreate(['key' => 'latitude_sekolah'], ['value' => $request->latitude_sekolah]);
        Pengaturan::updateOrCreate(['key' => 'longitude_sekolah'], ['value' => $request->longitude_sekolah]);
        Pengaturan::updateOrCreate(['key' => 'radius_meter'], ['value' => $request->radius_meter]);

        return redirect()->back()->with('success', 'Lokasi absensi sekolah berhasil diperbarui.');
    }
}