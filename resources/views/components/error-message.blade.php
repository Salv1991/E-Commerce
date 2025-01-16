@if(session('success') || session('error'))    
    <div id='flash-message' class="z-30 rounded-lg {{session('success') ? 'bg-black' : 'bg-red-500'}} text-white p-5  ">
        {{ session('success') ?? session('error') }}
    </div>

    <script>
        const flashMessage = document.getElementById('flash-message');

        if (flashMessage) {
            setTimeout( () => {
                flashMessage.style.display = 'none';
            }, 3000);
        }
    </script>
@endif