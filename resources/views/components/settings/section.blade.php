<x-layout>
    <section class="max-w-screen-xl m-auto p-10">
        <h1 class="text-left text-4xl font-semibold">Settings</h1>
          
        <ul class="flex justify-start items-center gap-5 *:text-base *:font-semibold mt-20">
            <li>
                <a href="{{route('settings.customer-information.show')}}" class="hover:text-primary-500 hover:underline underline-offset-8 {{Route::is('settings.customer-information.show') ? 'underline' : ''}}">Customer Information</a>
            </li>
            <li>
                <a href="{{route('settings.account.show')}}" class="hover:text-primary-500 hover:underline underline-offset-8 {{Route::is('settings.account.show') ? 'underline' : ''}}">Account</a>
            </li>
        </ul>

        {{ $slot }}

    </section>
</x-layout>