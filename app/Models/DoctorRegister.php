<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class DoctorRegister extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;
    
    protected $table='tbl_doctor_details';
    protected $fillable = [
        'user_id',
        'specilization',
        'doctor_folio_number',
        'work_experience',
        'doctor_licence_certificate',
        'alternative_number',
        'doctor_image',
        'available_location_lat',
        'available_location_lat',
        'available_location_long',
        'available_address',
        'qualification',
        'rating',
    ];
}
