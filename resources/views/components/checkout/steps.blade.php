@if ($currentStep && $steps)  
    <div class="flex justify-center items-start w-[300px] md:w-[650px] mt-10 m-auto">
        @foreach ($steps as $step)
            <div class="w-1/4 flex flex-col justify-center items-center">
                <div class="w-full relative h-7 z-0">
                    <div class="rounded-full {{$loop->iteration > $currentStep ? 'bg-red-300' : 'bg-primary-500'}} w-7 h-7 m-auto "></div>
                    @if ($loop->iteration > 1)
                        <div class="absolute top-[13px] -left-[32%] md:-left-[42%] h-1 w-full {{$loop->iteration > $currentStep ? 'bg-red-300' : 'bg-primary-500'}}"></div>
                    @endif
                </div>          
                <p class="font-semibold mt-2 text-center text-xs md:text-base max-w-28">{{$step}}</p>
            </div>
        @endforeach  
    </div>  
@endif
