<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function orders() {
        $orders = Auth::user()->orders()->whereNot('status', 'pending')->get();

        return view('user.orders', compact(['orders']));
    }

    public function showCustomerInformation() {
        $customerData = Auth::user()->getCustomerData();

        return view('user.settings.customer-information', compact(['customerData']));
    }

    public function editCustomerInformation(Request $request) {
       $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'floor' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'mobile' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user()->load('customerInformation');

        if ($validatedData['email'] !== $user->email) {
            abort(403, "Email mismatch.");
        }

        $user->update(['name' => $validatedData['name']]);

        $user->customerInformation()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'address' => $validatedData['address'],
                'postal_code' => $validatedData['postal_code'],
                'floor' => $validatedData['floor'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'mobile' => $validatedData['mobile'],
                'alternative_phone' => $validatedData['alternative_phone'],
            ]
        );
        
        return redirect()->route('settings.customer-information.show')->with('success', 'Your information has updated successfully.');
    }

    public function account() {

        return view('user.settings.account', compact([]));
    }

    public function deleteAccount() {
        Auth::user()->delete();
        Auth::logout();
        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
