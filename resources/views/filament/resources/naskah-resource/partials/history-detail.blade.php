<x-filament::card>
    <table class="w-full text-left space-y-2">
        <tbody>
            <tr>
                <td class="font-semibold">Tanggal</td>
                <td>:</td>
                <td>{{ $record->created_at->format('d M y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="font-semibold">User</td>
                <td>:</td>
                <td>{{ $record->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Deskripsi</td>
                <td>:</td>
                <td>{{ $record->description }}</td>
            </tr>
            <tr>
                <td class="font-semibold">IP Address</td>
                <td>:</td>
                <td>{{ $record->ip_address }}</td>
            </tr>
            <tr>
                <td class="font-semibold">User Agent</td>
                <td>:</td>
                <td>{{ $record->user_agent }}</td>
            </tr>
        </tbody>
    </table>
</x-filament::card>
