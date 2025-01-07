<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInformation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address',
        'postal_code',
        'floor',
        'country',
        'city',
        'mobile',
        'alternative_phone',
    ];
}
