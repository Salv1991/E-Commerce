<div {{ $attributes->merge(['class' => 'flex flex-col justify-start items-start mt-5']) }} >
    <label class="text-xs font-bold pl-2">
    {{ $label }}
    @if($attributes->has('required'))
        <span>*</span>
    @endif
    </label>
    <input 
        type="text" 
        placeholder="{{ $placeholder }}" 
        name="{{ $name }}"
        value="{{ old($name) }}"
        @if($attributes->has('required'))
         required
        @endif
        class="w-full mt-2 px-5 py-4 rounded-full bg-gray-50 focus:outline-none focus:ring-1 focus:ring-red-300 focus:border-red-300">
        @if ($errors->any())
            <span class="pl-2 text-sm text-red-500">
                {{ $errors->first($name) }}
            </span>
        @endif
</div>
