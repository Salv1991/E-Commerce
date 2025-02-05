<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="New Category">
    <form class="mt-10" method="post" action="{{route('admin.category.create.store')}}" enctype="multipart/form-data">
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

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form.input readonly type="number" class="col-span-full sm:col-span-1" label="Depth" name="depth" value="{{ old('depth') }}" placeholder="Depth"/>
                <x-form.input required type="number" class="col-span-full sm:col-span-1" label="Weight" name="weight" value="{{ old('weight') }}" placeholder="Weight"/>
                <x-form.input readonly class="col-span-full sm:col-span-1" label="Slug" name="slug" value="{{ old('slug') }}" placeholder="Slug"/>
            </div>
               
            <label for="parent_category" class="block text-xs font-bold pl-2 mt-5">Parent category *</label>
                <select name="parent_id" id="parent_category" class="w-full mt-2 p-2">
                    <option value="">No parent</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" class="text-black">
                            {{ $category->title }}
                        </option>
                        @if($category->children->isNotEmpty())
                            @foreach ($category->children as $child)
                                <option value="{{ $child->id }}" class="text-black">
                                    {{ $child->title }}
                                </option>
                            @endforeach
                        @endif
                    @endforeach
                </select>

            @error('parent_id')
                <div class="text-red-500 text-sm mt-2">
                    {{ $message }}
                </div>
            @enderror
            
            <div class="ml-auto w-full sm:w-fit">
                <button type="submit" class="bg-black mt-10 w-full rounded-sm hover:underline underline-offset-4 px-20 py-4 text-white">Save changes</button>
            </div>
        </form>
   </x-admin.layout>
</x-layout>