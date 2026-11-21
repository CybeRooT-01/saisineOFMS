@extends('layouts.app')

@section('title', 'Liste des demandes')

@section('content')
<div class="p-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Liste des demandes</h1>
        </div>
        <a 
            href="{{ route('saisines.create') }}" 
            class="inline-flex items-center px-4 py-3 text-sm font-medium text-white rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-[1.02]"
            style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
        >
            <svg class="icon-md mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle demande
        </a>
    </div>

    @if($saisines->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Référence
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Titre
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Étape
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Urgence
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Créé le
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($saisines as $saisine)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap w-32">
                        <div class="text-sm font-medium text-gray-900">{{ $saisine->reference_number }}</div>
                    </td>
                    <td class="px-4 py-3 max-w-xs">
                        <div class="text-sm text-gray-900">
                            <a href="{{ route('saisines.show', $saisine) }}" class="font-medium hover:text-[--color-orange-primary] transition-colors">
                                {{ Str::limit($saisine->title, 40) }}
                            </a>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Par {{ $saisine->creator->name }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $saisine->requestType->display_name }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $saisine->currentStage->display_name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($saisine->urgency_level === 'critical')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="icon-sm mr-1" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Critique
                            </span>
                        @elseif($saisine->urgency_level === 'priority')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <svg class="icon-sm mr-1" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Prioritaire
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Standard
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $saisine->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                        @php
                            $canView = app(\App\Services\AuthorizationService::class)->canViewSaisine(Auth::user(), $saisine);
                        @endphp
                        
                        <div class="flex items-center justify-end">
                            <div class="flex items-center justify-end gap-2 w-28">
                                @if($canView)
                                    <a href="{{ route('saisines.show', $saisine) }}" class="inline-flex items-center justify-center w-8 h-8 text-white rounded-lg transition-all duration-200 hover:shadow-md" style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);" title="Voir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if($saisine->creator_id === Auth::id())
                                        <a href="{{ route('saisines.edit', $saisine) }}" class="inline-flex items-center justify-center w-8 h-8 text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all duration-200" title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button 
                                            type="button"
                                            onclick="openDeleteModal({{ $saisine->id }}, '{{ $saisine->reference_number }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-all duration-200" 
                                            title="Supprimer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Vous n'avez pas accès à cette demande">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $saisines->links() }}
    </div>
    @else
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto icon-xl text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle demande.</p>
        <div class="mt-6">
            <a 
                href="{{ route('saisines.create') }}" 
                class="inline-flex items-center px-4 py-3 text-sm font-medium text-white rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-[1.02]"
                style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
            >
                <svg class="icon-md mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle demande
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Confirmer la suppression</h3>
            <p class="text-sm text-gray-500 text-center mt-2">
                Êtes-vous sûr de vouloir supprimer la demande <span id="deleteReference" class="font-semibold text-gray-900"></span> ?
            </p>
            <p class="text-sm text-red-600 text-center mt-2">
                Cette action est irréversible.
            </p>
            <div class="flex gap-3 mt-6">
                <button 
                    type="button"
                    onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium"
                >
                    Annuler
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium"
                    >
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(saisineId, reference) {
    document.getElementById('deleteReference').textContent = reference;
    document.getElementById('deleteForm').action = `/saisines/${saisineId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection
