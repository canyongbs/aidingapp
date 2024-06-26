<?php

namespace AidingApp\ServiceManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\LicenseSettings;
use Laravel\Pennant\Feature as PennantFeature;
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
        if (!PennantFeature::active('service-request-feedback') && !app(LicenseSettings::class)->data->addons->feedbackManagement) {
            return response()->json(['error' => 'Feedback Management is not enabled.'], 403);
        }
        return $next($request);
    }
}
