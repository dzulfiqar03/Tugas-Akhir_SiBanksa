@props(['items' => []])

@php
    // Hapus item yang label-nya kosong/null
    $filtered = array_filter($items, fn($item) => !empty($item['label']));
@endphp

<ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">

    @foreach ($filtered as $item)
        <li class="inline-flex items-center">

            {{-- Ikon Home hanya untuk item pertama --}}
            @if ($loop->first)
                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </svg>
            @endif

            {{-- Jika ada URL, jadikan link --}}
            @if (!empty($item['url']))
                <a href="{{ $item['url'] }}"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $item['label'] }}
                </span>
            @endif

            {{-- Separator di antara item --}}
            @if (!$loop->last)
                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 9 4-4-4-4" />
                </svg>
            @endif

        </li>
    @endforeach

</ol>
