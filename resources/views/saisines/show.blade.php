@extends('layouts.app')

@section('title', 'Détails de la demande')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $saisine->title }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $saisine->reference_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a 
                href="{{ route('saisines.export', $saisine) }}" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
                style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger PDF
            </a>
            <a 
                href="{{ route('dashboard') }}" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--color-orange-primary]"
            >
                Retour au tableau de bord
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de la demande</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $saisine->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Type de demande</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $saisine->requestType->display_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Créé par</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $saisine->creator->name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Créé le</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $saisine->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dernière mise à jour</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $saisine->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <!-- Custom Fields -->
                    @if(!empty($saisine->custom_fields))
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Champs additionnels</h3>
                        <div class="space-y-3">
                            @foreach($saisine->custom_fields as $key => $value)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Validation History -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Historique des validations</h2>
                
                @if($saisine->validations->count() > 0)
                <div class="space-y-4">
                    @foreach($saisine->validations->sortByDesc('created_at') as $validation)
                    <div class="border-l-4 @if($validation->action === 'validate' || $validation->action === 'total_validation') border-green-500 @elseif($validation->action === 'partial_validation') border-yellow-500 @else border-blue-500 @endif pl-4 py-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $validation->validator->name }}
                                    <span class="text-gray-500">à</span>
                                    {{ $validation->stage->display_name }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $validation->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($validation->action === 'validate' || $validation->action === 'total_validation') bg-green-100 text-green-800
                                @elseif($validation->action === 'partial_validation') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                @if($validation->action === 'validate') Validé
                                @elseif($validation->action === 'total_validation') Validation totale
                                @elseif($validation->action === 'partial_validation') Validation partielle
                                @else Demande de complément
                                @endif
                            </span>
                        </div>
                        @if($validation->comment)
                        <p class="mt-2 text-sm text-gray-700">{{ $validation->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">Aucune validation pour le moment.</p>
                @endif
            </div>

            <!-- Version History -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Historique des versions</h2>
                    <a href="{{ route('saisines.versions.index', $saisine) }}" class="text-sm text-[--color-orange-primary] hover:text-[--color-orange-dark]">
                        Voir tout
                    </a>
                </div>
                
                @if($versions->count() > 0)
                <div class="space-y-3">
                    @foreach($versions as $version)
                    <div class="flex items-start space-x-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-[--color-orange-primary] text-white text-xs font-medium">
                                v{{ $version->version_number }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $version->author->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $version->created_at->format('d/m/Y à H:i') }}
                            </p>
                            @if($version->change_description)
                            <p class="mt-1 text-sm text-gray-700">{{ $version->change_description }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">Aucun historique de version disponible.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Validation Actions -->
            @include('saisines.partials.validation-actions')

            <!-- Attachments -->
            @include('saisines.partials.attachments')

            <!-- Current Stage -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-sm font-medium text-gray-500 mb-3">Étape actuelle</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[--color-orange-primary] text-gray-600 ">
                    {{ $saisine->currentStage->display_name }}
                </span>
                @if($saisine->stage_entered_at)
                <p class="mt-3 text-xs text-gray-500">
                    Entrée le : {{ $saisine->stage_entered_at->format('d/m/Y') }}
                </p>
                @endif
            </div>

            <!-- Urgency Level -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-sm font-medium text-gray-500 mb-3">Niveau d'urgence</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($saisine->urgency_level === 'critical') bg-red-100 text-red-800
                    @elseif($saisine->urgency_level === 'priority') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($saisine->urgency_level === 'critical') Critique
                    @elseif($saisine->urgency_level === 'priority') Prioritaire
                    @else Standard
                    @endif
                </span>
                @if($saisine->isDelayed())
                <div class="mt-3 flex items-center text-xs text-red-600">
                    <svg class="icon-sm mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    En retard
                </div>
                @endif
            </div>

            <!-- Contributor Management -->
            @include('saisines.partials.contributor-management')
        </div>
    </div>
</div>
@endsection
