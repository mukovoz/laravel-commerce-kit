<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emulate — {{ $site->name }}</title>
    <style>
        body { font-family: sans-serif; max-width: 640px; margin: 40px auto; padding: 0 16px; color: #111; }
        h1 { font-size: 1.25rem; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        th { color: #6b7280; font-weight: 500; width: 180px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .installed { background: #d1fae5; color: #065f46; }
        .uninstalled { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Emulating: {{ $site->name }}</h1>
    <table>
        <tr><th>ID</th><td>{{ $site->id }}</td></tr>
        <tr><th>Platform</th><td>{{ $site->platform->label() }}</td></tr>
        <tr><th>Name</th><td>{{ $site->name }}</td></tr>
        <tr><th>URL</th><td>{{ $site->url }}</td></tr>
        <tr><th>Store Hash</th><td>{{ $site->store_hash ?? '—' }}</td></tr>
        <tr>
            <th>Status</th>
            <td>
                @if($site->isInstalled())
                    <span class="badge installed">Installed</span>
                @else
                    <span class="badge uninstalled">Uninstalled</span>
                @endif
            </td>
        </tr>
        <tr><th>Created</th><td>{{ $site->created_at->toDateTimeString() }}</td></tr>
        @if($site->uninstalled_at)
        <tr><th>Uninstalled At</th><td>{{ $site->uninstalled_at->toDateTimeString() }}</td></tr>
        @endif
    </table>
</body>
</html>
