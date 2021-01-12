<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class RegisterAmbulance extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;
    protected $table='tbl_ambulance_details';
    protected $fillable = [
        'user_id',
        'driver_name',
        'ambulance_number',
        'ambulance_reg_number',
        'driver_alternative_mobile',
        'driver_image',
    ];
}
