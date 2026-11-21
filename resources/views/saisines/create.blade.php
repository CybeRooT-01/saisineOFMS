@extends('layouts.app')

@section('title', 'Créer une nouvelle demande')

@section('content')
<style>
    /* Custom focus styles */
    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(255, 85, 0, 0.15);
    }
    
    /* Radio card styles */
    .radio-card input:checked + .radio-content {
        border-color: var(--check-color);
        background-color: var(--check-bg);
    }
    
    .radio-card input:checked + .radio-content .radio-indicator {
        border-color: var(--check-color);
        background-color: var(--check-color);
    }
    
    .radio-card input:checked + .radio-content .radio-indicator::after {
        opacity: 1;
        transform: scale(1);
    }
    
    /* Backdrop blur support */
    @supports (backdrop-filter: blur(12px)) {
        .glass-effect {
            backdrop-filter: blur(12px);
            background-color: rgba(255, 255, 255, 0.8);
        }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-orange-50/30 -mt-16 pt-16">
    <!-- Decorative background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-100 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute top-1/2 -left-20 w-60 h-60 bg-slate-100 rounded-full blur-3xl opacity-60"></div>
    </div>
    
    <main class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-[--color-orange-primary] to-orange-600 shadow-lg shadow-orange-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Nouvelle demande</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Remplissez les informations ci-dessous</p>
                </div>
            </div>
            
            <!-- Progress indicator -->
            <div class="flex items-center gap-2 mt-6">
                <div class="flex-1 h-1 rounded-full bg-[--color-orange-primary]"></div>
                <div class="flex-1 h-1 rounded-full bg-slate-200"></div>
                <div class="flex-1 h-1 rounded-full bg-slate-200"></div>
            </div>
        </header>

        <!-- Form Card -->
        <form method="POST" action="{{ route('saisines.store') }}">
            @csrf
            <div class="glass-effect bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                
                <!-- Form Content -->
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-[--color-orange-primary] text-xs font-bold">1</span>
                                    Informations générales
                                </h2>
                                <p class="text-slate-500 text-sm mt-1 ml-8">Décrivez votre demande</p>
                            </div>
                            
                            <!-- Request Type Select -->
                            <div class="relative" x-data="{ open: false, selected: '{{ old('request_type_id') }}', selectedText: '{{ old('request_type_id') ? $requestTypes->find(old('request_type_id'))->display_name : '' }}' }">
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Type de demande <span class="text-[--color-orange-primary]">*</span>
                                </label>
                                <input type="hidden" name="request_type_id" :value="selected" required>
                                
                                <button 
                                    type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm text-left hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors flex items-center justify-between @error('request_type_id') border-red-500 @enderror"
                                >
                                    <span :class="selected ? 'text-slate-900' : 'text-slate-400'" x-text="selectedText || 'Sélectionnez un type...'"></span>
                                    <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div 
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-20 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-200 max-h-60 overflow-auto"
                                    style="display: none;"
                                >
                                    @foreach($requestTypes as $type)
                                    <button
                                        type="button"
                                        @click="selected = '{{ $type->id }}'; selectedText = '{{ $type->display_name }}'; open = false; loadMandatoryFields('{{ $type->id }}')"
                                        class="w-full px-4 py-3 text-left text-sm hover:bg-orange-50 transition-colors flex items-center justify-between group"
                                        :class="selected == '{{ $type->id }}' ? 'bg-orange-50 text-[--color-orange-primary] font-medium' : 'text-slate-700'"
                                    >
                                        <span>{{ $type->display_name }}</span>
                                        <svg x-show="selected == '{{ $type->id }}'" class="w-5 h-5 text-[--color-orange-primary]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    @endforeach
                                </div>
                                
                                @error('request_type_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Title Input -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Titre <span class="text-[--color-orange-primary]">*</span>
                                </label>
                                <input type="text" name="title" required maxlength="255"
                                    value="{{ old('title') }}"
                                    placeholder="Ex: Révision du contrat fournisseur XYZ"
                                    class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description Textarea -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Description <span class="text-[--color-orange-primary]">*</span>
                                </label>
                                <textarea name="description" rows="5" required
                                    placeholder="Décrivez votre demande en détail..."
                                    class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-[--color-orange-primary] text-xs font-bold">2</span>
                                    Niveau d'urgence
                                </h2>
                                <p class="text-slate-500 text-sm mt-1 ml-8">Définissez la priorité</p>
                            </div>

                            <!-- Urgency Cards -->
                            <div class="space-y-3">
                                <!-- Critical -->
                                <label class="radio-card block cursor-pointer" style="--check-color: #ef4444; --check-bg: #fef2f2;">
                                    <input type="radio" name="urgency_level" value="critical" {{ old('urgency_level') == 'critical' ? 'checked' : '' }} class="sr-only">
                                    <div class="radio-content flex items-start gap-4 p-4 rounded-xl border-2 border-slate-200 hover:border-slate-300 transition-all">
                                        <div class="radio-indicator relative flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 border-slate-300 transition-colors">
                                            <span class="absolute inset-1 rounded-full bg-white opacity-0 transform scale-0 transition-all"></span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-red-600">Critique</span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-100 rounded-full">Urgent</span>
                                            </div>
                                            <p class="text-sm text-slate-500 mt-1">Nécessite une attention immédiate dans les 24h</p>
                                        </div>
                                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                </label>

                                <!-- Priority -->
                                <label class="radio-card block cursor-pointer" style="--check-color: #ff5500; --check-bg: #fff7ed;">
                                    <input type="radio" name="urgency_level" value="priority" {{ old('urgency_level') == 'priority' ? 'checked' : '' }} class="sr-only">
                                    <div class="radio-content flex items-start gap-4 p-4 rounded-xl border-2 border-slate-200 hover:border-slate-300 transition-all">
                                        <div class="radio-indicator relative flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 border-slate-300 transition-colors">
                                            <span class="absolute inset-1 rounded-full bg-white opacity-0 transform scale-0 transition-all"></span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-[--color-orange-primary]">Prioritaire</span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[--color-orange-primary] bg-orange-100 rounded-full">48h</span>
                                            </div>
                                            <p class="text-sm text-slate-500 mt-1">Important, nécessite un examen rapide</p>
                                        </div>
                                        <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </label>

                                <!-- Standard -->
                                <label class="radio-card block cursor-pointer" style="--check-color: #64748b; --check-bg: #f8fafc;">
                                    <input type="radio" name="urgency_level" value="standard" {{ old('urgency_level', 'standard') == 'standard' ? 'checked' : '' }} class="sr-only">
                                    <div class="radio-content flex items-start gap-4 p-4 rounded-xl border-2 border-slate-200 hover:border-slate-300 transition-all">
                                        <div class="radio-indicator relative flex-shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 border-slate-300 transition-colors">
                                            <span class="absolute inset-1 rounded-full bg-white opacity-0 transform scale-0 transition-all"></span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-slate-700">Standard</span>
                                            </div>
                                            <p class="text-sm text-slate-500 mt-1">Délai de traitement normal (5-7 jours)</p>
                                        </div>
                                        <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            @error('urgency_level')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Additional Info Box -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">Besoin d'aide ?</p>
                                        <p class="text-sm text-slate-500 mt-0.5">Contactez notre équipe juridique pour toute question sur le niveau d'urgence approprié.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Mandatory Fields Container -->
                    <div id="mandatory-fields-container" class="mt-8"></div>
                </div>

                <!-- Form Footer -->
                <div class="px-6 sm:px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 order-2 sm:order-1">
                            <span class="text-[--color-orange-primary]">*</span> Champs obligatoires
                        </p>
                        <div class="flex items-center gap-3 order-1 sm:order-2 w-full sm:w-auto">
                            <a href="{{ route('dashboard') }}" class="flex-1 sm:flex-none px-6 py-3 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400 transition-all text-center">
                                Annuler
                            </a>
                            <button type="submit" class="flex-1 sm:flex-none group relative px-8 py-3 text-sm font-semibold text-white rounded-xl shadow-lg shadow-orange-500/25 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--color-orange-primary] transition-all overflow-hidden" style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    Créer la demande
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
    // Load mandatory fields dynamically based on request type
    function loadMandatoryFields(requestTypeId) {
        const container = document.getElementById('mandatory-fields-container');
        
        if (!requestTypeId) {
            container.innerHTML = '';
            return;
        }

        // Show loading state
        container.innerHTML = '<div class="text-center py-4"><p class="text-sm text-slate-500">Chargement...</p></div>';

        // Fetch mandatory fields for the selected request type
        fetch(`/api/request-types/${requestTypeId}/mandatory-fields`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(fields => {
                if (fields.length === 0) {
                    container.innerHTML = '';
                    return;
                }

                let html = '<div class="border-t border-slate-200 pt-8">';
                html += '<h3 class="text-lg font-semibold text-slate-900 mb-6">Champs obligatoires supplémentaires</h3>';
                html += '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">';
                
                fields.forEach(field => {
                    html += '<div class="relative">';
                    html += `<label class="block text-sm font-medium text-slate-700 mb-2">${field.label} <span class="text-[--color-orange-primary]">*</span></label>`;
                    
                    if (field.type === 'textarea') {
                        html += `<textarea name="custom_fields[${field.name}]" rows="4" class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors resize-none"></textarea>`;
                    } else {
                        html += `<input type="${field.type}" name="custom_fields[${field.name}]" class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors">`;
                    }
                    
                    html += '</div>';
                });
                
                html += '</div></div>';
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du chargement des champs obligatoires:', error);
                container.innerHTML = '';
            });
    }

    // Load mandatory fields if request type is already selected
    document.addEventListener('DOMContentLoaded', function() {
        const selectedValue = document.querySelector('select[name="request_type_id"]')?.value;
        if (selectedValue) {
            loadMandatoryFields(selectedValue);
        }
    });
</script>
@endsection
