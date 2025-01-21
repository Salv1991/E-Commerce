<x-settings.section>
    <form class="mt-10" method="post" action="{{route('settings.customer-information.edit')}}">
        @method('PATCH')
        @csrf
        <x-form.input 
            required
            readonly
            label="E-mail" 
            name="email" 
            value="{{ $customerData['email'] ?? old('email') }}" 
            placeholder="E-mail"/> 

        <x-form.input required label="Name" name="name" value="{{ $customerData['name'] ?? old('name') }}" placeholder="Name"/>
        
        <div class="grid grid-cols-3 gap-4">
            <x-form.input required class="col-span-2" label="Address" name="address" value="{{ $customerData['address'] ?? old('address') }}" placeholder="Address"/>
            <x-form.input required label="Postal code" name="postal_code" value="{{ $customerData['postal_code'] ?? old('postal_code') }}" placeholder="Postal code"/>
        </div>
        
        <x-form.input label="Floor" name="floor" value="{{ $customerData['floor'] ?? old('floor') }}" placeholder="Optional"/>
        
        <div class="grid grid-cols-2 gap-4">                             
            <x-form.input required label="Country" name="country" value="{{ $customerData['country'] ?? old('country') }}" placeholder="Country"/>
            <x-form.input required label="City" name="city" value="{{ $customerData['city'] ?? old('city') }}" placeholder="City"/>
            <x-form.input required label="Mobile phone" name="mobile" value="{{ $customerData['mobile'] ?? old('mobile') }}" placeholder="Mobile phone"/>
            <x-form.input label="Alternative phone" name="alternative_phone" value="{{ $customerData['alternative_phone'] ?? old('alternative_phone') }}" placeholder="Alternative phone"/>
        </div>
        
        <div class="ml-auto w-full sm:w-fit">
            <button type="submit" class="bg-black mt-10 w-full sm  px-20 py-4 text-white">Edit</button>
        </div>
    </form>
</x-settings.section>
