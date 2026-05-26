<?php

namespace Lantera\ExtensionFramework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Models\Site;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppsManagerSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('platforms.apps_manager.application_secret');


        if (!$secret || !hash_equals($secret, (string) $request->header('X-Application-Secret'))) {
            abort(401, 'Unauthorized');
        }

        $token = $request->header('X-Application-Access-Token');
        $site = $token ? Site::where('apps_manager_access_token', $token)->first() : null;

        if (!$site) {
            abort(401, 'Unauthorized');
        }

        $request->merge(['site' => $site]);

        return $next($request);
    }
}
