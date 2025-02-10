<x-layout :hideHeader="true" :hideFooter="true">
   <x-admin.layout title="Categories">
        <a href="{{route('admin.category.create.show')}}" class="ml-auto bg-black hover:bg-black/80  text-white w-fit px-4 py-2 flex justify-between items-center gap-2">
            <x-heroicon-m-plus class="w-5 h-5"/>
            <span>Create Category</span>
        </a>
        <div class="overflow-x-auto w-full pb-4">
            <table class="table-auto w-full mt-5 min-w-[800px]">
                <thead class="">
                    <tr class="*:text-end bg-gray-300 *:px-3">
                        <th>Id</th>
                        <th>Parent_id</th>
                        <th>Depth</th>
                        <th>Weight</th>
                        <th>Slug</th>
                        <th>Title</th>
                        <th class="max-w-56">Description</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2">
                    @forelse($categories as $category)
                        <tr class="*:text-end hover:bg-gray-200">
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->id}}</a></td>
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->parent_id ?? '-'}}</a></td>
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->depth}}</a></td>
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->weight}}</a></td>
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->slug}}</a></td>
                            <td><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">{{$category->title}}</a></td>
                            <td ><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full max-h-6 px-3 overflow-hidden">{{$category->description}}</a></td>
                            <td class="line-clamp-1"><a href="{{route('admin.category.edit.show', $category->id)}}" class="block w-full h-full px-3">"{{$category->image_path}}"</a></td>
                        </tr>
                    @empty
                    <tr class="">
                        <td colspan="7" class="text-center p-2">No categories found</td>
                    </tr> 
                    @endforelse
                </tbody>
            </table> 
        </div>    

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div> 

   </x-admin.layout>
</x-layout>