<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'mpn',
        'description',
        'current_price',
        'discount',
        'original_price',
        'stock'
    ];
    
    protected static function booted() {
        static::updated(function ($product) {
            if ($product->isDirty('current_price')) { 
                foreach ($product->lineItems as $lineItem) {
                    $lineItem->price = $product->current_price;
                    $lineItem->save();
                }
            }
        });
    }
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
