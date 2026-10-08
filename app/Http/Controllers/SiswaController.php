<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lomba;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $lombas = Lomba::orderBy('nama_lomba')->get();
        $riwayat = Pendaftaran::with('lomba')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('siswa.dashboard', compact('lombas', 'riwayat'));
    }

    public function daftarLomba(Request $request)
    {
        $validated = $request->validate([
            'lomba_id' => ['required', 'integer', 'exists:lombas,id'],
            'kelas' => ['required', 'string', 'max:50'],
            'no_wa' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s()-]+$/'],
        ]);

        $userId = Auth::id();
        $lombaId = $validated['lomba_id'];

        $sudahDaftar = Pendaftaran::where('user_id', $userId)
            ->where('lomba_id', $lombaId)
            ->exists();

        if ($sudahDaftar) {
            return back()->with('error', 'Gagal: Anda sudah terdaftar di mata lomba ini.');
        }

        Pendaftaran::create([
            'user_id' => $userId,
            'lomba_id' => $lombaId,
            'kelas' => $validated['kelas'],
            'no_wa' => $validated['no_wa'],
            'status' => 'Dokumen dalam Tinjauan',
        ]);

        return back()->with('success', 'Berhasil mendaftar! Silakan pantau status Anda di bawah.');
    }
}
