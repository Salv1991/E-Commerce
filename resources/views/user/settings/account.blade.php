<x-settings.section>
    <div data-controller="delete-account">
        <form id="delete-account-form" class="mt-10" action="{{route('settings.account.delete')}}" method="post" >
            @csrf
            @method('DELETE')
            <button type="submit" class="text-primary-500 hover:text-red-600">Delete account</button>    
        </form>

        <div data-delete-account-target="confirmPrompt" class="fixed inset-0 bg-black/50 flex flex-col justify-center items-center p-5 rounded-md hidden">
            <div class="bg-white p-5">
                <span>Are you sure you want to delete your account?</span>
                <div class="*:border *:px-8 *:py-2 grid grid-cols-2 gap-2 *:col-span-full *:xs:col-span-1 mt-4">
                    <button data-action="click->delete-account#cancel" class="text-gray-600 border-gray-600 hover:bg-gray-100/50">Cancel</button>
                    <button data-action="click->delete-account#delete" class="border-red-500 text-white bg-red-500 hover:bg-red-600 font-semibold">Delete</button>
                </div>
            </div>
        </div>
    </div>
</x-settings.section>
