<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateWishlist implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $user;
    protected $id;
    protected $status;
    
    public function __construct($user, $id, $status)
    {
        $this->user = $user;
        $this->id = $id;
        $this->status = $status; 
    }

    public function handle(): void
    {
        try{            
            if($this->status ==='added') {
                $this->user->wishlistedProducts()->attach($this->id);        
            } else if($this->status === 'removed'){
                $this->user->wishlistedProducts()->detach($this->id);       
            }
            Cache::forget('wishlisted-product-ids-' . Auth::id());

        } catch (\Exception $e) {
            logger('Wishlist update failed');
        }

    }
}
