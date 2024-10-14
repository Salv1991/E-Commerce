<div class="flex flex-col justify-center items-start gap-2 mt-5">
    <label class="text-xs font-bold">{{ $label }}</label>
    <input 
        type="text" 
        placeholder="{{ $placeholder }}" 
        name="{{ $name }}"
        value="{{ old($name) }}"
        class="w-full px-5 py-4 rounded-full bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
</div>
@if ($errors->any())
    <div class="text-red-500">
        {{ $errors->first($name) }}
    </div>
@endif