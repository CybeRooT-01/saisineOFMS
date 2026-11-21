<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
        </svg>
        Pièces jointes
    </h2>

    <!-- Upload Form -->
    <div x-data="{ showUploadForm: false }" class="mb-4">
        <button 
            @click="showUploadForm = !showUploadForm"
            type="button"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg"
            style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajouter un fichier
        </button>

        <div x-show="showUploadForm" x-cloak class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <form method="POST" action="{{ route('saisines.attachments.store', $saisine) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Sélectionner un fichier <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        id="file"
                        name="file" 
                        accept=".pdf,.xlsx,.xls,.docx,.doc"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">
                        Formats acceptés: PDF, Excel (.xlsx, .xls), Word (.docx, .doc) - Max 10MB
                    </p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="showUploadForm = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-md transition-all duration-200"
                        style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                    >
                        Télécharger
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attachments List -->
    @if($saisine->attachments->count() > 0)
    <div class="space-y-2">
        @foreach($saisine->attachments as $attachment)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- File Icon -->
                <div class="flex-shrink-0">
                    @php
                        $ext = strtolower($attachment->getExtension());
                    @endphp
                    @if($ext === 'pdf')
                        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    @elseif(in_array($ext, ['xlsx', 'xls']))
                        <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    @elseif(in_array($ext, ['docx', 'doc']))
                        <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>

                <!-- File Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $attachment->original_filename }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $attachment->getFormattedSize() }} • Ajouté par {{ $attachment->uploader->name }} • {{ $attachment->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Download Button -->
                <a 
                    href="{{ route('saisines.attachments.download', [$saisine, $attachment]) }}"
                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200"
                    title="Télécharger"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>

                <!-- Delete Button (only for uploader or creator) -->
                @if($attachment->uploaded_by === Auth::id() || $saisine->creator_id === Auth::id())
                <form method="POST" action="{{ route('saisines.attachments.destroy', [$saisine, $attachment]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')"
                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-all duration-200"
                        title="Supprimer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
        </svg>
        <p class="mt-2 text-sm text-gray-500">Aucune pièce jointe</p>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
