<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function __construct(protected CartService $cartService){}

    public function show() {
        return view('user.signup');
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'password_confirmation' => ['required', 'min:8'],
        ]);
        
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'is_admin' => false,
            'is_guest' => false,
        ]);

        Auth::login($user);

        $guest = session()->get('guest', []);

        if(!empty($guest['cart'])){
            $this->cartService->mergeCarts($guest['cart']);
        }

        session()->flash('success', 'Registration successful! Welcome, ' . $user->name . '!');

        return redirect('/');
    }

}