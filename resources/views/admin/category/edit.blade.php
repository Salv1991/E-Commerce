<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="{{$category->title}} (id:{{$category->id}})"  href="{{route('category', $category->id)}}">
    <form class="mt-10" method="post" action="{{route('admin.category.edit.store', $category->id)}}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf

            <div class="relative m-auto">
                <img 
                    src="{{ asset('storage/' . $category->image_path) }}" 
                    alt="Category Image" 
                    class="w-56 min-w-56 object-cover rounded-md" />
            </div>

            <label for="image" class="block text-xs font-bold pl-2 mt-5">Upload New Image</label>
            <input type="file" name="image" id="image" accept="image/*" 
                class="w-full mt-2 px-4 py-2 border rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200">
                
            <x-form.input required label="Title" name="title" value="{{ old('title', $category->title) }}" placeholder="Title"/>
            
            <label for="description" class="block text-xs font-bold pl-2 mt-5">Description *</label>
            <textarea required id="description" class="w-full mt-2 h-52 px-5 py-4 rounded-md bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200" name="description">{{ old('description', $category->description) }}</textarea>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form.input readonly type="number" class="col-span-full sm:col-span-1" label="Depth" name="depth" value="{{ old('depth' , $category->depth) }}" placeholder="Depth"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-1" label="Weight" name="weight" value="{{ old('weight' , $category->weight) }}" placeholder="Weight"/>
                <x-form.input readonly class="col-span-full sm:col-span-1" label="Slug" name="slug" value="{{ old('slug' , $category->slug) }}" placeholder="Slug"/>
            </div>
                     
            <x-admin.button />

        </form>
   </x-admin.layout>
</x-layout>