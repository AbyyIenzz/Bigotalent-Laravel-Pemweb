<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $lomba_id
 * @property string|null $kelas
 * @property string|null $no_wa
 * @property string $status
 */
class Pendaftaran extends Model
{
    // 1. Mengizinkan kolom diisi data
    protected $fillable = ['user_id', 'lomba_id', 'kelas', 'no_wa', 'status'];

    // 2. Relasi balik ke User (Siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Relasi balik ke Lomba
    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }
}
