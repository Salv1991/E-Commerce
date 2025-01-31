<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="New product">
    <form class="mt-10" method="post" action="{{route('admin.product.store')}}" enctype="multipart/form-data">
            @csrf

            <label for="image" class="block text-xs font-bold pl-2 mt-5">Upload New Image</label>
            <input  type="file" name="image" id="image" accept="image/*" 
                class="w-full mt-2 px-4 py-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200">
                
            @error('image')
                <div class="text-red-500 text-sm mt-2">
                    {{ $message }}
                </div>
            @enderror

            <x-form.input required label="Title" name="title" value="{{ old('title') }}" placeholder="Title"/>
            
            <label for="description" class="block text-xs font-bold pl-2 mt-5">Description *</label>
            <textarea required id="description" class="w-full mt-2 h-52 px-5 py-4 rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200" name="description">{{ old('description') }}</textarea>

            <div class="grid grid-cols-4 lg:grid-cols-5 gap-4">
                <x-form.input required type="number" class="col-span-full sm:col-span-2 lg:col-span-1" label="Current price" name="current_price" value="{{ old('current_price') }}" placeholder="Current price"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-2 lg:col-span-1" label="Original price" name="original_price" value="{{ old('original_price') }}" placeholder="Original price"/>
                <x-form.input readonly type="number" class="col-span-full sm:col-span-2 lg:col-span-1" label="Discount" name="discount" value="{{ old('discount') }}" placeholder="Discount"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-2 lg:col-span-1" label="Stock" name="stock" value="{{ old('stock') }}" placeholder="Stock"/>
                <x-form.input required class="col-span-full lg:col-span-1" label="mpn" name="mpn" value="{{ old('mpn') }}" placeholder="mpn"/>
            </div>
                     
            <div class="ml-auto w-full sm:w-fit">
                <button type="submit" class="bg-black mt-10 w-full rounded-sm hover:underline underline-offset-4 px-20 py-4 text-white">Save changes</button>
            </div>
        </form>
   </x-admin.layout>
</x-layout>