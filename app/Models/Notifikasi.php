<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    protected $primaryKey = 'idNotifikasi';
    public    $timestamps = false;

    protected $fillable = ['idPenerima', 'jenis', 'isi', 'waktu', 'statusBaca'];
}