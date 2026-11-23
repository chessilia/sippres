<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perkuliahan extends Model
{
    protected $table      = 'perkuliahan';
    protected $primaryKey = 'idPerkuliahan';
    public    $timestamps = false;

    protected $fillable = [
        'kodeMatkul', 'namaMatkul', 'idDosen',
        'hari', 'jamMulai', 'jamSelesai', 'ruang', 'kelas',
    ];

    // relasi: perkuliahan diampu oleh satu dosen (User)
    public function dosen()
    {
        return $this->belongsTo(User::class, 'idDosen', 'idUser');
    }
}