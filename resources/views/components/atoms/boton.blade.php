@props(['color' => 'blue', 'type' => 'submit', 'id' => null, 'ancho' => 'auto'])

@php
    $colores = [
        'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'outline' => 'border border-slate-300 text-slate-700',
    ];
    $anchoClase = $ancho === 'full' ? 'w-full' : '';
@endphp

<button
    type="{{ $type }}"
    @if($id) id="{{ $id }}" @endif
    class="{{ $colores[$color] }} {{ $anchoClase }} font-bold py-2 rounded-lg text-sm transition"
>
    {{ $slot }}
</button>