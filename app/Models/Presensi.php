<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table      = 'presensi';
    protected $primaryKey = 'idPresensi';
    public    $timestamps = false;

    protected $fillable = [
        'idMahasiswa', 'idSesi', 'waktu', 'lokasi', 'status', 'keterangan',
    ];

    protected $casts = ['waktu' => 'datetime'];

    public function sesi()
    {
        return $this->belongsTo(SesiPresensi::class, 'idSesi', 'idSesi');
    }
}