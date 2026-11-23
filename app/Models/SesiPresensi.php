<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table      = 'sesi_presensi';
    protected $primaryKey = 'idSesi';
    public    $timestamps = false;

    protected $fillable = [
        'idPerkuliahan', 'kodeQR', 'waktuMulai', 'durasi',
        'status', 'latitude', 'longitude', 'radius',
    ];

    protected $casts = ['waktuMulai' => 'datetime'];

    public function perkuliahan()
    {
        return $this->belongsTo(Perkuliahan::class, 'idPerkuliahan', 'idPerkuliahan');
    }
}