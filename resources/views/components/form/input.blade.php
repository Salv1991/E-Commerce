<div {{ $attributes->merge(['class' => 'flex flex-col justify-start items-start mt-5']) }} >
    <label class="text-xs font-bold pl-2">
    {{ $label }}
    @if($attributes->has('required'))
        <span>*</span>
    @endif
    </label>
    <input 
        type="{{ $type ?? 'text'}}" 
        placeholder="{{ $placeholder }}" 
        name="{{ $name }}"
        value="{{ $value }}"

        @if($attributes->has('required')) 
            required 
        @endif

        @if($attributes->has('readonly')) 
            readonly 
        @endif
        
        class="w-full mt-2 px-5 py-4 rounded-full bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-200 focus:border-gray-200">
        @if ($errors->any())
            <span class="pl-2 text-sm text-red-500">
                {{ $errors->first($name) }}
            </span>
        @endif
</div>
