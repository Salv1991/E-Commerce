<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\ModelsPruned;
use Illuminate\Support\Arr;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'mpn',
        'price',
        'discounted_price',
    ];
    
    //protected static function booted() {
        //static::updated(function ($product) {
           // if ($product->isDirty('current_price')) { 
                //foreach ($product->lineItems as $lineItem) {
                    //$lineItem->price = $product->current_price;
                   // $lineItem->save();
               // }
           // }
        //});
   // }
    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function image() {
        return $this->hasOne(ProductImage::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function lineItem() {
        return $this->hasOne(LineItem::class);
    }
    
    public function lineItems() {
        return $this->hasMany(LineItem::class);
    }
}
