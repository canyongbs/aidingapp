<?php

namespace AidingApp\ServiceManagement\Http\Middleware;

use App\Settings\LicenseSettings;
use Closure;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestTypeFeedbackIsOn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $serviceRequest = $request->route('serviceRequest');

        if (Feature::active('service-request-feedback') && app(LicenseSettings::class)->data->addons->feedbackManagement) {
            if ($serviceRequest && $serviceRequest?->priority?->type?->has_enabled_feedback_collection) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Feedback collection is not enabled for this service request.'], 403);
    }
}
