<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table      = 'user';      // nama tabel sesuai PDM
    protected $primaryKey = 'idUser';    // PK sesuai PDM
    public    $timestamps = false;       // PDM tidak punya created_at/updated_at

    protected $fillable = ['username', 'nama', 'email', 'password', 'role', 'fotoProfil'];
    protected $hidden   = ['password'];

    // password otomatis di-hash saat disimpan
    protected $casts = ['password' => 'hashed'];
}