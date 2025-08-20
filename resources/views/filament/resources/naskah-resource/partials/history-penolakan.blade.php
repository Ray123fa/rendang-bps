<div class="overflow-x-auto">
    <table class="text-sm border border-gray-200 rounded" style="min-width: 100%;">
        <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
                <th
                    class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-left text-gray-800 dark:text-gray-200">
                    Tanggal
                </th>
                <th
                    class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-left text-gray-800 dark:text-gray-200">
                    File
                </th>
                <th
                    class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-left text-gray-800 dark:text-gray-200">
                    Alasan
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900">
            @forelse ($riwayat as $item)
            @php
                $fileUrl = $item->file ? asset('storage/' . $item->file) : null;
            @endphp
            <tr>
                <td class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                    {{ $item->created_at->format('d/m/y H:i') }}
                </td>
                <td class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                    @if ($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline" title="Lihat File">
                            {{ basename($item->file) }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-3 py-2 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                    {{ $item->keterangan }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-3 py-2 text-center border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                    Belum ada riwayat penolakan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
