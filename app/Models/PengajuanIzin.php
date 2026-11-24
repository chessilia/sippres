<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    protected $table      = 'pengajuan_izin';
    protected $primaryKey = 'idIzin';
    public    $timestamps = false;

    protected $fillable = [
        'idMahasiswa', 'idPerkuliahan', 'pertemuan', 'alasan',
        'fileBukti', 'status', 'tanggalPengajuan',
    ];

    protected $casts = ['tanggalPengajuan' => 'datetime'];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'idMahasiswa', 'idUser');
    }

    public function perkuliahan()
    {
        return $this->belongsTo(Perkuliahan::class, 'idPerkuliahan', 'idPerkuliahan');
    }
}