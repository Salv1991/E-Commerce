@if(session('success') || session('error'))    
    <div id='flash-message-container' class="px-5 w-full xs:w-fit text-white text-sm xs:text-base 
        text-center absolute -bottom-24 m-auto right-0 translate-x-full transform duration-300 opacity-0">
        <div id='flash-message' class="{{session('success') ? 'bg-black' : 'bg-red-400'}} p-5 rounded-lg">
            {{ session('success') ?? session('error') }}
        </div>
    </div>

    <script>
        const flashMessage = document.getElementById('flash-message-container');

        if (flashMessage) {
            setTimeout( () => {
                flashMessage.classList.toggle('translate-x-full');
                flashMessage.classList.toggle('opacity-0');
            }, 100);

            setTimeout( () => {
                flashMessage.classList.toggle('translate-x-full');
                flashMessage.classList.toggle('opacity-0');
            }, 3000);
        }
    </script>
@endif
        