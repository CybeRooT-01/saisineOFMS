@php
    $canAddContributors = app(App\Services\AuthorizationService::class)->canAddContributors(Auth::user(), $saisine);
    $allUsers = App\Models\User::where('id', '!=', Auth::id())->get();
@endphp

<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contributeurs et observateurs</h2>
    
    <!-- Add Contributor Form -->
    @if($canAddContributors)
    <div x-data="{ showAddForm: false }" class="mb-6">
        <button 
            @click="showAddForm = !showAddForm"
            type="button"
            class="w-full px-4 py-2 text-sm font-medium text-white bg-[#ff5500] rounded-md hover:bg-[#e64d00] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff5500]"
        >
            Ajouter un contributeur/observateur
        </button>

        <div x-show="showAddForm" x-cloak class="mt-4 border-t border-gray-200 pt-4">
            <form method="POST" action="{{ route('saisines.contributors.store', $saisine) }}">
                @csrf
                
                <!-- User Selection -->
                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Sélectionner un utilisateur <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="user_id"
                        name="user_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff5500] focus:border-[#ff5500]"
                        required
                    >
                        <option value="">-- Sélectionner un utilisateur --</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contributor Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="contributor_type" 
                                value="contributor" 
                                class="h-4 w-4 text-[#ff5500] focus:ring-[#ff5500] border-gray-300"
                                required
                                checked
                            >
                            <span class="ml-2 text-sm text-gray-700">
                                Contributeur <span class="text-gray-500">(accès complet)</span>
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="contributor_type" 
                                value="observer" 
                                class="h-4 w-4 text-[#ff5500] focus:ring-[#ff5500] border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700">
                                Observateur <span class="text-gray-500">(lecture seule)</span>
                            </span>
                        </label>
                    </div>
                    @error('contributor_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="showAddForm = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#ff5500] rounded-md hover:bg-[#e64d00] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff5500]"
                    >
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Current Contributors List -->
    @if($saisine->contributors->count() > 0)
    <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Contributeurs</h3>
        <ul class="space-y-2">
            @foreach($saisine->contributors as $contributor)
            <li class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md" x-data="{ showDeleteModal: false }">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-[#ff5500] flex items-center justify-center text-white text-sm font-medium">
                            {{ strtoupper(substr($contributor->name, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $contributor->name }}</p>
                        <p class="text-xs text-gray-500">{{ $contributor->email }}</p>
                    </div>
                </div>
                @if($canAddContributors)
                <button 
                    type="button"
                    @click="showDeleteModal = true"
                    class="text-sm text-red-600 hover:text-red-800"
                >
                    Retirer
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
                                Confirmer le retrait
                            </h3>
                            <p class="text-gray-600 text-center mb-6">
                                Êtes-vous sûr de vouloir retirer <span class="font-semibold text-gray-900">{{ $contributor->name }}</span> des contributeurs ?
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
                                <form method="POST" action="{{ route('saisines.contributors.destroy', [$saisine, $contributor]) }}" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="w-full px-4 py-3 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg"
                                    >
                                        Retirer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Current Observers List -->
    @if($saisine->observers->count() > 0)
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-3">Observateurs</h3>
        <ul class="space-y-2">
            @foreach($saisine->observers as $observer)
            <li class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md" x-data="{ showDeleteModal: false }">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-sm font-medium">
                            {{ strtoupper(substr($observer->name, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $observer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $observer->email }}</p>
                    </div>
                </div>
                @if($canAddContributors)
                <button 
                    type="button"
                    @click="showDeleteModal = true"
                    class="text-sm text-red-600 hover:text-red-800"
                >
                    Retirer
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
                                Confirmer le retrait
                            </h3>
                            <p class="text-gray-600 text-center mb-6">
                                Êtes-vous sûr de vouloir retirer <span class="font-semibold text-gray-900">{{ $observer->name }}</span> des observateurs ?
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
                                <form method="POST" action="{{ route('saisines.contributors.destroy', [$saisine, $observer]) }}" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="w-full px-4 py-3 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg"
                                    >
                                        Retirer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Empty State -->
    @if($saisine->contributors->count() === 0 && $saisine->observers->count() === 0)
    <p class="text-sm text-gray-500 text-center py-4">Aucun contributeur ou observateur ajouté pour le moment.</p>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
