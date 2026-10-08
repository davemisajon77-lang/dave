<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'specialization', 'email'];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'doctor_service');
    }
}
