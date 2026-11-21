@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des utilisateurs</h1>
    </div>

    <!-- Add New User Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200" x-data="{ showAddForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Ajouter un nouvel utilisateur</h2>
            <button 
                @click="showAddForm = !showAddForm"
                class="px-6 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
            >
                <span x-show="!showAddForm">+ Nouvel utilisateur</span>
                <span x-show="showAddForm">Annuler</span>
            </button>
        </div>

        <div x-show="showAddForm" x-transition class="border-t border-gray-200 pt-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            placeholder="ex: Jean Dupont"
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                        >
                    </div>

                    <!-- Login -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                            Identifiant <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            required
                            placeholder="ex: jdupont"
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            placeholder="ex: jean.dupont@example.com"
                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                        >
                    </div>
                </div>

                <!-- Structure -->
                <div>
                    <label for="structure_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Structure (optionnel)
                    </label>
                    <select 
                        id="structure_id" 
                        name="structure_id"
                        class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                    >
                        <option value="">Aucune structure</option>
                        @foreach($structures as $structure)
                            <option value="{{ $structure->id }}">{{ $structure->fullpath }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Roles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Rôles <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 cursor-pointer transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                            <input 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $role->id }}"
                                class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                            >
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-900">{{ $role->display_name }}</span>
                                @if($role->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <svg class="icon-sm inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Le mot de passe par défaut est "password" (l'authentification se fera via LDAP)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="px-8 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                        style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                    >
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Utilisateurs existants</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identifiant</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150" x-data="{ editing: false }">
                        <!-- Edit Form Row (Full Width) -->
                        <td colspan="6" x-show="editing" class="px-6 py-6">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nom complet <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            value="{{ $user->name }}"
                                            required
                                            placeholder="ex: Jean Dupont"
                                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                        >
                                    </div>

                                    <!-- Login -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Identifiant <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="login" 
                                            value="{{ $user->login }}"
                                            required
                                            placeholder="ex: jdupont"
                                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                        >
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            value="{{ $user->email }}"
                                            required
                                            placeholder="ex: jean.dupont@example.com"
                                            class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                        >
                                    </div>
                                </div>

                                <!-- Structure -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Structure (optionnel)
                                    </label>
                                    <select 
                                        name="structure_id"
                                        class="w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                    >
                                        <option value="">Aucune structure</option>
                                        @foreach($structures as $structure)
                                            <option value="{{ $structure->id }}" {{ $user->structure_id == $structure->id ? 'selected' : '' }}>
                                                {{ $structure->fullpath }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Roles -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Rôles <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($roles as $role)
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-orange-300 cursor-pointer transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                            <input 
                                                type="checkbox" 
                                                name="roles[]" 
                                                value="{{ $role->id }}"
                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                                class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                                            >
                                            <div class="ml-3">
                                                <span class="text-sm font-medium text-gray-900">{{ $role->display_name }}</span>
                                                @if($role->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-3 pt-4 border-t border-gray-200">
                                    <button 
                                        type="submit"
                                        class="px-8 py-3 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200"
                                        style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                                    >
                                        Enregistrer les modifications
                                    </button>
                                    <button 
                                        type="button"
                                        @click="editing = false"
                                        class="px-8 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                                    >
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </td>

                        <!-- Normal View Columns -->
                        <td class="px-6 py-4" x-show="!editing">
                            <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Vous
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4" x-show="!editing">
                            <code class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded font-mono">{{ $user->login }}</code>
                        </td>
                        <td class="px-6 py-4" x-show="!editing">
                            <span class="text-sm text-gray-600">{{ $user->email }}</span>
                        </td>
                        <td class="px-6 py-4" x-show="!editing">
                            @if($user->structure)
                            <code class="px-2 py-1 text-xs bg-blue-50 text-blue-800 rounded font-mono">{{ $user->structure->fullpath }}</code>
                            @else
                            <span class="text-xs text-gray-400">Aucune structure</span>
                            @endif
                        </td>
                        <td class="px-6 py-4" x-show="!editing">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $role->display_name }}
                                </span>
                                @empty
                                <span class="text-xs text-gray-400">Aucun rôle</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" x-show="!editing">
                            <div class="flex items-center justify-end gap-2">
                                <button 
                                    @click="editing = true"
                                    class="text-orange-600 hover:text-orange-900 transition-colors"
                                    title="Modifier"
                                >
                                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                @if($user->id !== auth()->id())
                                <div x-data="{ showDeleteModal: false }">
                                    <button 
                                        @click="showDeleteModal = true"
                                        class="text-red-600 hover:text-red-900 transition-colors"
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
                                                    Êtes-vous sûr de vouloir supprimer l'utilisateur <span class="font-semibold text-gray-900">{{ $user->name }}</span> ?
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
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="flex-1">
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
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Aucun utilisateur trouvé
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
