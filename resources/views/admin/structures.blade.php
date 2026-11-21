@extends('layouts.app')

@section('title', 'Gestion des structures')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des structures</h1>
    </div>

    <!-- Add New Structure Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200" x-data="{ showAddForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Ajouter une nouvelle structure</h2>
            <button 
                @click="showAddForm = !showAddForm"
                class="px-6 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
            >
                <span x-show="!showAddForm">+ Nouvelle structure</span>
                <span x-show="showAddForm">Annuler</span>
            </button>
        </div>

        <div x-show="showAddForm" x-transition class="border-t border-gray-200 pt-6">
            <form method="POST" action="{{ route('admin.structures.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Libelle -->
                    <div>
                        <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="libelle" 
                            name="libelle" 
                            required
                            placeholder="ex: DSI, DEV, RH..."
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                        >
                    </div>

                    <!-- Parent Structure -->
                    <div>
                        <label for="parent_structure_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Structure parente (optionnel)
                        </label>
                        <select 
                            id="parent_structure_id" 
                            name="parent_structure_id"
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                        >
                            <option value="">Aucune (structure racine)</option>
                            @foreach($structures as $struct)
                                <option value="{{ $struct->id }}">{{ $struct->fullpath }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Si vide, la structure sera à la racine</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="3"
                        placeholder="Description de la structure"
                        class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 resize-none"
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="px-8 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                        style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                    >
                        Créer la structure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Structures List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Structures existantes</h2>
            <p class="text-sm text-gray-500 mt-1">Le chemin complet (fullpath) est calculé automatiquement</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chemin complet</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($structures as $structure)
                    <tr class="hover:bg-gray-50 transition-colors duration-150" x-data="{ editing: false }">
                        <td class="px-6 py-4">
                            <div x-show="!editing">
                                <code class="px-2 py-1 text-sm bg-blue-50 text-blue-800 rounded font-mono">{{ $structure->fullpath }}</code>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div x-show="!editing">
                                <span class="text-sm font-medium text-gray-900">{{ $structure->libelle }}</span>
                            </div>
                            <div x-show="editing">
                                <form method="POST" action="{{ route('admin.structures.update', $structure) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input 
                                        type="text" 
                                        name="libelle" 
                                        value="{{ $structure->libelle }}"
                                        required
                                        placeholder="Libellé"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                    >
                                    <select 
                                        name="parent_structure_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                    >
                                        <option value="">Aucune (racine)</option>
                                        @foreach($structures as $struct)
                                            @if($struct->id !== $structure->id)
                                            <option value="{{ $struct->id }}" {{ $structure->parent_structure_id == $struct->id ? 'selected' : '' }}>
                                                {{ $struct->fullpath }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <textarea 
                                        name="description" 
                                        rows="2"
                                        placeholder="Description"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"
                                    >{{ $structure->description }}</textarea>
                                    <div class="flex gap-2">
                                        <button 
                                            type="submit"
                                            class="px-4 py-2 text-xs font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors"
                                        >
                                            Enregistrer
                                        </button>
                                        <button 
                                            type="button"
                                            @click="editing = false"
                                            class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                        >
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div x-show="!editing">
                                <span class="text-sm text-gray-600">{{ $structure->description ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $structure->users_count }} utilisateur(s)
                            </span>
                            @if($structure->children->count() > 0)
                            <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $structure->children->count() }} sous-structure(s)
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div x-show="!editing" class="flex items-center justify-end gap-2">
                                <button 
                                    @click="editing = true"
                                    class="text-orange-600 hover:text-orange-900 transition-colors"
                                    title="Modifier"
                                >
                                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <div x-data="{ showDeleteModal: false }">
                                    <button 
                                        @click="showDeleteModal = true; if({{ $structure->users_count }} > 0 || {{ $structure->children->count() }} > 0) { 
                                            if({{ $structure->users_count }} > 0) {
                                                toastr.error('Impossible de supprimer cette structure car {{ $structure->users_count }} utilisateur(s) y sont rattachés');
                                            } else {
                                                toastr.error('Impossible de supprimer cette structure car elle est la structure parente de {{ $structure->children->count() }} sous-structure(s)');
                                            }
                                        }"
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        :class="{ 'opacity-50 cursor-not-allowed': {{ $structure->users_count > 0 || $structure->children->count() > 0 ? 'true' : 'false' }} }"
                                        title="{{ ($structure->users_count > 0 || $structure->children->count() > 0) ? 'Impossible de supprimer (utilisateurs ou sous-structures rattachés)' : 'Supprimer' }}"
                                    >
                                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                    <!-- Delete Confirmation Modal (only if no users and no children) -->
                                    @if($structure->users_count == 0 && $structure->children->count() == 0)
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
                                                    Êtes-vous sûr de vouloir supprimer la structure <span class="font-semibold text-gray-900">{{ $structure->fullpath }}</span> ?
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
                                                    <form method="POST" action="{{ route('admin.structures.destroy', $structure) }}" class="flex-1">
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
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Aucune structure trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
