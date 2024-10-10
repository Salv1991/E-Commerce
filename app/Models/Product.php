<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\ModelsPruned;
use Illuminate\Support\Arr;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'discounted_price'
    ];
   
}
