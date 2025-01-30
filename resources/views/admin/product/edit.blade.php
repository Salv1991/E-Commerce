<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="{{$product->title}} (id:{{$product->id}})">
    <form class="mt-10" method="post" action="{{route('admin.product.edit', $product->id)}}">
            @method('PATCH')
            @csrf

            <div class="relative m-auto">
                <img 
                    src="{{ asset('storage/' . ($product->images->isNotEmpty() ? $product->images->first()->image_path : 'products/placeholder.jpg') )}}" 
                    alt="Product Image" 
                    class="w-56 object-cover rounded-md" />
            </div>

            <label for="image" class="block text-xs font-bold pl-2 mt-5">Upload New Image</label>
            <input type="file" name="image" id="image" accept="image/*" 
                class="w-full mt-2 px-4 py-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200" 
                onchange="previewImage(event)">
                
            <x-form.input required label="Title" name="title" value="{{ old('title', $product->title) }}" placeholder="Title"/>
            
            <label for="description" class="block text-xs font-bold pl-2 mt-5">Description *</label>
            <textarea required id="description" class="w-full mt-2 h-52 px-5 py-4 rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200" name="description">{{ old('description', $product->description) }}</textarea>

            <div class="grid grid-cols-4 gap-4">
                <x-form.input required type="number" class="col-span-full sm:col-span-1" label="Current price" name="current_price" value="{{ old('address', $product->current_price) }}" placeholder="Current price"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-1" label="Original price" name="original_price" value="{{ old('original_price', $product->original_price) }}" placeholder="Original price"/>
                <x-form.input readonly type="number" class="col-span-full sm:col-span-1" label="Discount" name="discount" value="{{ old('discount', $product->discount) }}" placeholder="Discount"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-1" label="Stock" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="Stock"/>
            </div>
                     
            <div class="ml-auto w-full sm:w-fit">
                <button type="submit" class="bg-black mt-10 w-full rounded-sm hover:underline underline-offset-4 px-20 py-4 text-white">Save changes</button>
            </div>
        </form>
   </x-admin.layout>
</x-layout>