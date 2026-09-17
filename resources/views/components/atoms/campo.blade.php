@props(['label', 'name', 'type' => 'text'])

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">{{ $label }}</label>

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}" id="{{ $name }}" required rows="3"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
        ></textarea>
    @else
        <input
            type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
    @endif

    <p id="error-{{ $name }}" class="hidden text-red-600 text-xs mt-1"></p>
</div>