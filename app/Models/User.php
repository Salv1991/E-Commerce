<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_guest',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function customerInformation() {
        return $this->hasOne(CustomerInformation::class);    
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function currentOrder() {
        return $this->hasOne(Order::class)->where('status', 'pending');
    }

    public function wishlistedProducts() {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function wishlistedProductsIds() {
        return $this->wishlistedProducts()->pluck('product_id');
    }

    public function cartQuantity() {
        return $this->currentOrder()->sum('quantity');
    } 

    public function getCustomerData() {
        $customerInformation = $this->customerInformation()->first();
        
        return [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $customerInformation->address ?? null,
            'postal_code' =>$customerInformation->postal_code  ?? null,
            'floor' => $customerInformation->floor ?? null,
            'country' => $customerInformation->country ?? null,
            'city' => $customerInformation->city ?? null,
            'mobile' => $customerInformation->mobile ?? null,
            'alternative_phone' => $customerInformation->alternative_phone ?? null,
        ];
    }
}
