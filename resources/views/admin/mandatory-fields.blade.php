@extends('layouts.app')

@section('title', 'Configuration - Champs obligatoires')

@section('content')
<style>
    /* Custom focus styles */
    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(255, 85, 0, 0.15);
    }
</style>

<div class="p-8" x-data="{ showAddForm: false, selectedType: '' }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Configuration des champs obligatoires</h1>
    </div>

    <!-- Add Button -->
    <div class="mb-6">
        <button 
            @click="showAddForm = !showAddForm"
            class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all"
            style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
        >
            <svg class="icon-md mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajouter un champ obligatoire
        </button>
    </div>

    <!-- Add Form -->
    <div 
        x-show="showAddForm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-white shadow-xl rounded-3xl border border-gray-200 p-8 mb-8"
        style="display: none;"
    >
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Nouveau champ obligatoire</h2>
        
        <form method="POST" action="{{ route('admin.mandatory-fields.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Request Type -->
                <div class="relative">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Type de demande <span class="text-[--color-orange-primary]">*</span>
                    </label>
                    <select 
                        name="request_type_id" 
                        required
                        class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm appearance-none cursor-pointer hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors"
                    >
                        <option value="">Sélectionnez...</option>
                        @foreach($requestTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-4 top-[2.85rem] w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <!-- Field Type -->
                <div class="relative">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Type de champ <span class="text-[--color-orange-primary]">*</span>
                    </label>
                    <select 
                        name="field_type" 
                        required
                        class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm appearance-none cursor-pointer hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors"
                    >
                        <option value="">Sélectionnez...</option>
                        <option value="text">Texte</option>
                        <option value="email">Email</option>
                        <option value="number">Nombre</option>
                        <option value="date">Date</option>
                        <option value="textarea">Zone de texte</option>
                    </select>
                    <svg class="absolute right-4 top-[2.85rem] w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <!-- Field Name -->
                <div class="relative">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nom du champ (technique) <span class="text-[--color-orange-primary]">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="field_name" 
                        required
                        placeholder="Ex: contract_number, amount..."
                        class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors"
                    >
                </div>

                <!-- Field Label -->
                <div class="relative">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Libellé du champ <span class="text-[--color-orange-primary]">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="field_label" 
                        required
                        placeholder="Ex: Numéro de contrat, Montant..."
                        class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors"
                    >
                </div>
            </div>

            <!-- Validation Rules -->
            <div class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Règles de validation (optionnel)
                </label>
                <input 
                    type="text" 
                    name="validation_rules" 
                    placeholder="Ex: required|min:3, required|email..."
                    class="input-field w-full px-4 py-3.5 bg-white rounded-xl border border-slate-200 text-slate-900 text-sm placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:border-[--color-orange-primary] transition-colors"
                >
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4">
                <button 
                    type="button"
                    @click="showAddForm = false"
                    class="px-6 py-3 text-sm font-medium text-slate-600 bg-white border-2 border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all"
                >
                    Annuler
                </button>
                <button 
                    type="submit"
                    class="px-8 py-3 text-sm font-semibold text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
                    style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                >
                    Ajouter
                </button>
            </div>
        </form>
    </div>

    <!-- List of Mandatory Fields by Request Type -->
    <div class="space-y-6">
        @foreach($requestTypes as $requestType)
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ $requestType->display_name }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $requestType->mandatoryFields->count() }} champ(s) obligatoire(s)
                </p>
            </div>

            @if($requestType->mandatoryFields->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($requestType->mandatoryFields as $field)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors" x-data="{ showDeleteModal: false }">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $field->field_type }}
                            </span>
                            <span class="font-medium text-gray-900">{{ $field->field_label }}</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-500">
                            Nom technique: <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $field->field_name }}</code>
                            @if($field->validation_rules)
                                <span class="ml-2">• Règles: <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $field->validation_rules }}</code></span>
                            @endif
                        </div>
                    </div>
                    
                    <button 
                        @click="showDeleteModal = true"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Supprimer"
                    >
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    <!-- Delete Confirmation Modal -->
                    <div 
                        x-show="showDeleteModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 overflow-y-auto"
                        style="display: none;"
                    >
                        <div class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div 
                                x-show="showDeleteModal"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 z-10"
                                @click.stop
                            >
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">
                                    Confirmer la suppression
                                </h3>
                                <p class="text-gray-600 text-center mb-6">
                                    Êtes-vous sûr de vouloir supprimer le champ <span class="font-semibold text-gray-900">{{ $field->field_label }}</span> ?
                                    <br><br>
                                    Cette action est irréversible.
                                </p>
                                <div class="flex gap-3">
                                    <button 
                                        type="button"
                                        @click="showDeleteModal = false"
                                        class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                                    >
                                        Annuler
                                    </button>
                                    <form method="POST" action="{{ route('admin.mandatory-fields.destroy', $field) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="w-full px-4 py-3 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg"
                                        >
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-8 text-center text-gray-500">
                <svg class="mx-auto icon-xl text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-sm">Aucun champ obligatoire configuré</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
