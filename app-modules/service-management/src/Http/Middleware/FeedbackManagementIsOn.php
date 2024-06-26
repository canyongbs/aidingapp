<?php

namespace AidingApp\ServiceManagement\Http\Middleware;

use Closure;
use App\Enums\Feature;
use App\Settings\LicenseSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FeedbackManagementIsOn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!app(LicenseSettings::class)->data->addons->feedbackManagement) {
            return response()->json(['error' => 'Feedback Management is not enabled.'], 403);
        }

        return $next($request);
    }
}
