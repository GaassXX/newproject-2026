@props(['headers' => [], 'rows' => []])

<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($rows as $row)
                <tr class="hover:bg-gray-50">
                    {{ $slot }}
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-4 text-center text-gray-500">
                        No data found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
