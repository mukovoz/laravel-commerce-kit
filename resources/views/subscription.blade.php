@php
    try {
        $script = file_get_contents(config('platforms.apps_manager.script_url') . '?url=' . $site->url);
        if ($script === false) {
            throw new RuntimeException('Error while getting subscription information');
        }
    } catch (Throwable $e) {
        throw new RuntimeException('Error while getting subscription information', previous: $e);
    }
@endphp
{!! $script !!}
