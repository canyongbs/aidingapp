@php
    use AidingApp\ServiceManagement\Models\ServiceRequest;
    use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
@endphp

@if ($this->getRecord()?->status?->classification === SystemServiceRequestClassification::Closed)
    <x-filament::icon-button
        data-identifier="service_request_closed"
        icon="heroicon-m-lock-closed"
        color="gray"
        size="lg"
        tooltip="This service request is locked as the status is {{ $this->getRecord()->status->classification }}."
    />
@endif
