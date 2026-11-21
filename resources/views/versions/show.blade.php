@extends('layouts.app')

@section('title', 'Version ' . $version->version_number . ' - ' . $saisine->reference_number)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Instantané de la version {{ $version->version_number }}</h1>
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

        <!-- Version Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-sm text-gray-500">Numéro de version</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $version->version_number }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Auteur</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $version->author->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Créé le</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">{{ $version->created_at->format('d/m/Y H:i') }}</div>
                    <div class="text-sm text-gray-500">{{ $version->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @if($version->change_description)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">Modifications</div>
                    <div class="mt-1 text-gray-900">{{ $version->change_description }}</div>
                </div>
            @endif
        </div>

        <!-- Content Snapshot -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Instantané du contenu</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-6">
                    @foreach($version->content_snapshot as $key => $value)
                        <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
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
                                $fieldLabel = $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                            @endphp
                            
                            <dt class="text-sm font-medium text-gray-500 mb-2">
                                {{ $fieldLabel }}
                            </dt>
                            <dd class="text-sm text-gray-900">
                                @if($key === 'attachments')
                                    @if(empty($value))
                                        <span class="text-gray-400 italic">Aucune pièce jointe</span>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($value as $att)
                                                <li class="flex items-center p-2 bg-gray-50 rounded">
                                                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="flex-1">{{ $att['original_filename'] ?? 'N/A' }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">
                                                        {{ isset($att['file_size']) ? number_format($att['file_size'] / 1024, 2) . ' KB' : '' }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @elseif(is_array($value))
                                    <pre class="bg-gray-50 p-3 rounded-md overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @elseif(is_null($value))
                                    <span class="text-gray-400 italic">Non défini</span>
                                @elseif($key === 'request_type_id')
                                    @php
                                        $requestType = \App\Models\RequestType::find($value);
                                    @endphp
                                    {{ $requestType ? $requestType->display_name : $value }}
                                @elseif($key === 'current_stage_id')
                                    @php
                                        $stage = \App\Models\WorkflowStage::find($value);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $stage ? $stage->display_name : $value }}
                                    </span>
                                @elseif($key === 'urgency_level')
                                    @if($value === 'critical')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Critique
                                        </span>
                                    @elseif($value === 'priority')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Prioritaire
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Standard
                                        </span>
                                    @endif
                                @elseif($key === 'expected_completion_date' && $value)
                                    {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
