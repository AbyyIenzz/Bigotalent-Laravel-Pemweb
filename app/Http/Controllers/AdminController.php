<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lomba;           // Memanggil Model Lomba
use App\Models\Pendaftaran;     // Memanggil Model Pendaftaran
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'jumlahLomba' => Lomba::count(),
            'jumlahSiswa' => User::where('role', 'siswa')->count(),
            'jumlahPendaftaran' => Pendaftaran::count(),
        ]);
    }

    public function kelolaLomba()
    {
        $lombas = Lomba::orderBy('nama_lomba')->get();

        return view('admin.kelola_lomba', compact('lombas'));
    }

    public function kelolaSiswa()
    {
        $siswas = User::where('role', 'siswa')->orderBy('name')->get();

        return view('admin.kelola_siswa', compact('siswas'));
    }

    public function kelolaStatus()
    {
        $pendaftarans = Pendaftaran::with(['user', 'lomba'])->latest()->get();

        return view('admin.kelola_status', compact('pendaftarans'));
    }

    public function storeLomba(Request $request)
    {
        $validated = $request->validate([
            'nama_lomba' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Lomba::create($validated);

        return back()->with('success', 'Mata lomba berhasil ditambahkan!');
    }

    public function editLomba($id)
    {
        $lomba = Lomba::findOrFail($id);

        return view('admin.edit_lomba', compact('lomba'));
    }

    public function updateLomba(Request $request, $id)
    {
        $lomba = Lomba::findOrFail($id);
        $validated = $request->validate([
            'nama_lomba' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $lomba->update($validated);

        return redirect()->route('admin.lomba')->with('success', 'Mata lomba berhasil diperbarui!');
    }

    public function destroyLomba($id)
    {
        Lomba::findOrFail($id)->delete();

        return back()->with('success', 'Mata lomba berhasil dihapus!');
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'Dokumen dalam Tinjauan',
                'Jadwal Briefing',
                'Tahap Pelatihan',
                'Final',
            ])],
        ]);

        $pendaftaran->update($validated);

        return back()->with('success', 'Status pendaftaran berhasil diubah!');
    }

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        return back()->with('success', 'Akun siswa berhasil dibuat!');
    }

    public function editSiswa($id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        return view('admin.edit_siswa', compact('siswa'));
    }

    public function updateSiswa(Request $request, $id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($siswa->id),
            ],
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        $siswa->name = $validated['name'];
        $siswa->email = $validated['email'];

        if (! empty($validated['password'])) {
            $siswa->password = Hash::make($validated['password']);
        }

        $siswa->save();

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroySiswa($id)
    {
        User::where('role', 'siswa')->findOrFail($id)->delete();

        return back()->with('success', 'Akun siswa berhasil dihapus!');
    }
}
