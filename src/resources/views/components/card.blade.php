@props(['title' => null, 'icon' => null])

<div class="bg-white rounded-lg shadow p-6">
    @if ($title || isset($icon))
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @if ($icon)
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                    {{ $icon }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
