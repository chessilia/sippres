<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKendala extends Model
{
    protected $table      = 'laporan_kendala';
    protected $primaryKey = 'idLaporan';
    public    $timestamps = false;

    protected $fillable = [
        'idPelapor', 'kategori', 'deskripsi', 'foto', 'status', 'tindakLanjut', 'tanggalLapor',
    ];

    protected $casts = ['tanggalLapor' => 'datetime'];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'idPelapor', 'idUser');
    }

    // label yang dilihat pengguna: "Diproses" (PDM) ditampilkan sebagai "Ditangani" (UI)
    public function getLabelStatusAttribute(): string
    {
        return $this->status === 'Diproses' ? 'Ditangani' :$this->status;
    }

    // nama kelas CSS untuk badge (tanpa spasi)
    public function getKelasStatusAttribute(): string
    {
        return str_replace(' ', '', $this->status);
    }
}