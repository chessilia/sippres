<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table      = 'sesi_presensi';
    protected $primaryKey = 'idSesi';
    public    $timestamps = false;

    protected $fillable = [
        'idPerkuliahan',
        'kodeQR',
        'tanggal',
        'waktuMulai',
        'durasi',
        'status',
        'latitude',
        'longitude',
        'radius',
        'catatan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'waktuMulai' => 'datetime',
    ];

    public function perkuliahan()
    {
        return $this->belongsTo(Perkuliahan::class, 'idPerkuliahan', 'idPerkuliahan');
    }

    // waktu sesi berakhir = waktu mulai + durasi
    public function waktuBerakhir()
    {
        return $this->waktuMulai->copy()->addMinutes($this->durasi);
    }

    // sesi masih bisa dipakai presensi?
    public function masihBerlaku(): bool
    {
        return $this->status === 'Aktif' && now()->lessThan($this->waktuBerakhir());
    }
}