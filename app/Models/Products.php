<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Products extends Model
{
    use  HasApiTokens, HasFactory, Notifiable;
    protected $table = "tbl_drugs";
    protected $fillable = [
        'drugstore_details_id',
        'drug_name',
        'ip_power',
        'drug_company_name',
        'manufacturing_date',
        'expiry_date',
        'mfg_lic_no',
        'price_per_piece',
        'notes',
    ];
}
