@extends('layouts.app')

@section('title', 'Comparer les versions - ' . $saisine->reference_number)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Comparer les versions</h1>
                    <p class="mt-2 text-gray-600">{{ $saisine->reference_number }} - {{ $saisine->title }}</p>
                </div>
                <a href="{{ route('saisines.versions.index', $saisine) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-md transition duration-150">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à l'historique
                </a>
            </div>
        </div>

        <!-- Version Comparison Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Version 1 Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Version {{ $version1->version_number }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Ancienne
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center text-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $version1->author->name }}
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $version1->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Version 2 Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Version {{ $version2->version_number }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Récente
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center text-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $version2->author->name }}
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $version2->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Differences -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">
                    Différences
                    @if(empty($differences))
                        <span class="ml-2 text-sm font-normal text-gray-500">(Aucun changement détecté)</span>
                    @else
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ count($differences) }} champ{{ count($differences) > 1 ? 's' : '' }} modifié{{ count($differences) > 1 ? 's' : '' }})</span>
                    @endif
                </h2>
            </div>

            @if(empty($differences))
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune différence trouvée</h3>
                    <p class="mt-1 text-sm text-gray-500">Ces versions ont un contenu identique.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($differences as $field => $change)
                        <div class="p-6">
                            @php
                                // Translate field names to French
                                $fieldLabels = [
                                    'title' => 'Titre',
                                    'description' => 'Description',
                                    'request_type_id' => 'Type de demande',
                                    'urgency_level' => 'Niveau d\'urgence',
                                    'current_stage_id' => 'Étape actuelle',
                                    'expected_completion_date' => 'Date de complétion attendue',
                                    'custom_fields' => 'Champs personnalisés',
                                    'attachments' => 'Pièces jointes',
                                ];
                                $fieldLabel = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                            @endphp
                            
                            <h3 class="text-sm font-medium text-gray-900 mb-4">
                                {{ $fieldLabel }}
                            </h3>
                            
                            @if($field === 'attachments')
                                <!-- Special handling for attachments -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Old Attachments -->
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="h-4 w-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                            <span class="text-xs font-medium text-red-800">Version {{ $version1->version_number }}</span>
                                        </div>
                                        <div class="text-sm text-red-900">
                                            @if(empty($change['old']))
                                                <span class="italic text-red-600">Aucune pièce jointe</span>
                                            @else
                                                <ul class="space-y-1">
                                                    @foreach($change['old'] as $att)
                                                        <li class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ $att['original_filename'] ?? 'N/A' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- New Attachments -->
                                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="text-xs font-medium text-green-800">Version {{ $version2->version_number }}</span>
                                        </div>
                                        <div class="text-sm text-green-900">
                                            @if(empty($change['new']))
                                                <span class="italic text-green-600">Aucune pièce jointe</span>
                                            @else
                                                <ul class="space-y-1">
                                                    @foreach($change['new'] as $att)
                                                        <li class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ $att['original_filename'] ?? 'N/A' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Standard field comparison -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Old Value -->
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="h-4 w-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                            <span class="text-xs font-medium text-red-800">Version {{ $version1->version_number }}</span>
                                        </div>
                                        <div class="text-sm text-red-900">
                                            @if(is_null($change['old']))
                                                <span class="italic text-red-600">Non défini</span>
                                            @elseif(is_array($change['old']))
                                                <pre class="whitespace-pre-wrap break-words">{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                @php
                                                    $displayValue = $change['old'];
                                                    
                                                    // Convert IDs to readable names
                                                    if($field === 'request_type_id') {
                                                        $requestType = \App\Models\RequestType::find($change['old']);
                                                        $displayValue = $requestType ? $requestType->display_name : $change['old'];
                                                    } elseif($field === 'current_stage_id') {
                                                        $stage = \App\Models\WorkflowStage::find($change['old']);
                                                        $displayValue = $stage ? $stage->display_name : $change['old'];
                                                    } elseif($field === 'urgency_level') {
                                                        $urgencyLabels = [
                                                            'critical' => 'Critique',
                                                            'priority' => 'Prioritaire',
                                                            'standard' => 'Standard'
                                                        ];
                                                        $displayValue = $urgencyLabels[$change['old']] ?? $change['old'];
                                                    } elseif($field === 'expected_completion_date' && $change['old']) {
                                                        $displayValue = \Carbon\Carbon::parse($change['old'])->format('d/m/Y');
                                                    }
                                                @endphp
                                                {{ $displayValue }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- New Value -->
                                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="h-4 w-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="text-xs font-medium text-green-800">Version {{ $version2->version_number }}</span>
                                        </div>
                                        <div class="text-sm text-green-900">
                                            @if(is_null($change['new']))
                                                <span class="italic text-green-600">Supprimé</span>
                                            @elseif(is_array($change['new']))
                                                <pre class="whitespace-pre-wrap break-words">{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                            @else
                                                @php
                                                    $displayValue = $change['new'];
                                                    
                                                    // Convert IDs to readable names
                                                    if($field === 'request_type_id') {
                                                        $requestType = \App\Models\RequestType::find($change['new']);
                                                        $displayValue = $requestType ? $requestType->display_name : $change['new'];
                                                    } elseif($field === 'current_stage_id') {
                                                        $stage = \App\Models\WorkflowStage::find($change['new']);
                                                        $displayValue = $stage ? $stage->display_name : $change['new'];
                                                    } elseif($field === 'urgency_level') {
                                                        $urgencyLabels = [
                                                            'critical' => 'Critique',
                                                            'priority' => 'Prioritaire',
                                                            'standard' => 'Standard'
                                                        ];
                                                        $displayValue = $urgencyLabels[$change['new']] ?? $change['new'];
                                                    } elseif($field === 'expected_completion_date' && $change['new']) {
                                                        $displayValue = \Carbon\Carbon::parse($change['new'])->format('d/m/Y');
                                                    }
                                                @endphp
                                                {{ $displayValue }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
