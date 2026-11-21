<?php

use Illuminate\Support\Facades\Route;
use App\Models\RequestType;

// API endpoint for fetching mandatory fields by request type
Route::get('/request-types/{requestType}/mandatory-fields', function (RequestType $requestType) {
    return response()->json($requestType->getMandatoryFieldsArray());
});
