<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price'    
    ];

    public function lineItems() {
        return $this->hasMany(LineItem::class);
    }

    public function getOrderTotalAttribute() {
        return $this->total_price;    
    }

    public function lineItemsQuantity() {
        return $this->lineItems->sum('quantity');
    }

    public function recalculateTotalPrice() {
        $totalPrice = $this->lineItems->sum(function($lineItem){
            return $lineItem->quantity * $lineItem->price;
        });

        $this->update([
            'total_price' => $totalPrice    
        ]);
        //$totalPrice = $this->lineItems()
   // ->selectRaw('SUM(quantity * price) as total')
   // ->value('total') ?? 0;
    }
}
