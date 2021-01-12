<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ProductImages extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;
    protected $table = "tbl_drug_images";
    protected $fillable = [
        'drug_id',
        'image',
    ];
}
