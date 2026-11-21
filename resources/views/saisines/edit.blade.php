@extends('layouts.app')

@section('title', 'Modifier la demande')

@section('content')
<div class="min-h-screen relative overflow-hidden" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full blur-3xl opacity-30 -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-tr from-blue-100 to-blue-50 rounded-full blur-3xl opacity-30 translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Modifier la demande</h1>
            <p class="mt-2 text-sm text-gray-600">Référence: {{ $saisine->reference_number }}</p>
        </div>

        <form method="POST" action="{{ route('saisines.update', $saisine) }}" class="backdrop-blur-sm bg-white/80 shadow-xl rounded-2xl border border-gray-200/50 p-8">
        @csrf
        @method('PUT')

        <!-- Section 1: Type de demande (lecture seule) -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white font-bold text-lg shadow-lg">
                    1
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Type de demande</h2>
            </div>
            
            <div class="relative">
                <input 
                    type="text" 
                    value="{{ $saisine->requestType->display_name }}"
                    disabled
                    class="w-full px-4 py-3.5 text-gray-500 bg-gray-50 border border-gray-200 rounded-xl shadow-sm cursor-not-allowed"
                >
                <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                    <svg class="icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Le type de demande ne peut pas être modifié après la création
                </p>
            </div>
        </div>

        <!-- Section 2: Informations générales -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white font-bold text-lg shadow-lg">
                    2
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Informations générales</h2>
            </div>

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $saisine->title) }}"
                    required
                    maxlength="255"
                    placeholder="Entrez le titre de la demande"
                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('title') border-red-500 @enderror"
                >
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    required
                    placeholder="Décrivez votre demande en détail"
                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 resize-none @error('description') border-red-500 @enderror"
                >{{ old('description', $saisine->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Section 3: Niveau d'urgence -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white font-bold text-lg shadow-lg">
                    3
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Niveau d'urgence</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Critical -->
                <label class="relative cursor-pointer group">
                    <input 
                        type="radio" 
                        name="urgency_level" 
                        value="critical" 
                        {{ old('urgency_level', $saisine->urgency_level) == 'critical' ? 'checked' : '' }}
                        class="peer sr-only"
                    >
                    <div class="h-full p-6 bg-white border-2 border-gray-200 rounded-xl shadow-sm transition-all duration-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-lg hover:border-red-300 hover:shadow-md">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-red-100 text-red-600">
                                <svg class="icon-md" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-red-500 peer-checked:bg-red-500 flex items-center justify-center transition-all duration-200">
                                <svg class="icon-sm text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-red-600 mb-2">Critique</h3>
                        <p class="text-sm text-gray-600">Nécessite une attention immédiate</p>
                    </div>
                </label>

                <!-- Priority -->
                <label class="relative cursor-pointer group">
                    <input 
                        type="radio" 
                        name="urgency_level" 
                        value="priority" 
                        {{ old('urgency_level', $saisine->urgency_level) == 'priority' ? 'checked' : '' }}
                        class="peer sr-only"
                    >
                    <div class="h-full p-6 bg-white border-2 border-gray-200 rounded-xl shadow-sm transition-all duration-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-lg hover:border-orange-300 hover:shadow-md">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-orange-100 text-orange-600">
                                <svg class="icon-md" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center transition-all duration-200">
                                <svg class="icon-sm text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-orange-600 mb-2">Prioritaire</h3>
                        <p class="text-sm text-gray-600">Important, nécessite un examen rapide</p>
                    </div>
                </label>

                <!-- Standard -->
                <label class="relative cursor-pointer group">
                    <input 
                        type="radio" 
                        name="urgency_level" 
                        value="standard" 
                        {{ old('urgency_level', $saisine->urgency_level) == 'standard' ? 'checked' : '' }}
                        class="peer sr-only"
                    >
                    <div class="h-full p-6 bg-white border-2 border-gray-200 rounded-xl shadow-sm transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg hover:border-blue-300 hover:shadow-md">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600">
                                <svg class="icon-md" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center transition-all duration-200">
                                <svg class="icon-sm text-white opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-blue-600 mb-2">Standard</h3>
                        <p class="text-sm text-gray-600">Délai de traitement normal</p>
                    </div>
                </label>
            </div>
            @error('urgency_level')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mandatory Fields -->
        @if($saisine->requestType->mandatoryFields->count() > 0)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white font-bold text-lg shadow-lg">
                    4
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Champs obligatoires supplémentaires</h2>
            </div>
            
            @foreach($saisine->requestType->mandatoryFields as $field)
            <div class="mb-6">
                <label for="custom_fields_{{ $field->field_name }}" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $field->field_label }} <span class="text-red-500">*</span>
                </label>
                
                @if($field->field_type === 'textarea')
                    <textarea 
                        id="custom_fields_{{ $field->field_name }}" 
                        name="custom_fields[{{ $field->field_name }}]" 
                        rows="3"
                        placeholder="Entrez {{ strtolower($field->field_label) }}"
                        class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 resize-none @error('custom_fields.' . $field->field_name) border-red-500 @enderror"
                    >{{ old('custom_fields.' . $field->field_name, $saisine->custom_fields[$field->field_name] ?? '') }}</textarea>
                @else
                    <input 
                        type="{{ $field->field_type }}" 
                        id="custom_fields_{{ $field->field_name }}" 
                        name="custom_fields[{{ $field->field_name }}]" 
                        value="{{ old('custom_fields.' . $field->field_name, $saisine->custom_fields[$field->field_name] ?? '') }}"
                        placeholder="Entrez {{ strtolower($field->field_label) }}"
                        class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('custom_fields.' . $field->field_name) border-red-500 @enderror"
                    >
                @endif
                
                @error('custom_fields.' . $field->field_name)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
        </div>
        @endif

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
            <a 
                href="{{ route('saisines.show', $saisine) }}" 
                class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
            >
                Annuler
            </a>
            <button 
                type="submit" 
                class="px-8 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
            >
                Mettre à jour la demande
            </button>
        </div>
    </form>
    </div>
</div>
@endsection
