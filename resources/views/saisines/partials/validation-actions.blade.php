@php
    $canValidate = app(App\Services\AuthorizationService::class)->canValidateSaisine(Auth::user(), $saisine);
    $isDRJStudyStage = $saisine->currentStage->name === App\Models\WorkflowStage::DRJ_STUDY;
    $hasNextStage = $saisine->currentStage->getNextStage() !== null;
    $isBusinessSubmissionStage = $saisine->currentStage->name === App\Models\WorkflowStage::BUSINESS_SUBMISSION;
@endphp

@if($canValidate)
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions de validation</h2>
    
    @if(!$hasNextStage)
    <!-- No next stage available message -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex items-start">
            <svg class="icon-md text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-900 mb-1">Dernière étape atteinte</h3>
                <p class="text-sm text-blue-700">
                    Cette demande a atteint la dernière étape du workflow. Toutes les étapes de validation ont été clôturées.
                </p>
            </div>
        </div>
    </div>
    @endif
    
    <div x-data="{ 
        showValidateForm: false, 
        showCompletionForm: false,
        showRejectForm: false,
        validationAction: 'validate',
        validationComment: '',
        completionReason: '',
        rejectReason: ''
    }">
        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Validate Button -->
            <button 
                @click="@if($hasNextStage) showValidateForm = !showValidateForm; showCompletionForm = false; showRejectForm = false @else toastr.info('Cette demande a atteint la dernière étape du workflow. Toutes les étapes de validation ont été clôturées.') @endif"
                type="button"
                class="w-full px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200
                    @if($hasNextStage)
                        bg-[#ff5500] hover:bg-[#e64d00] focus:ring-[#ff5500]
                    @else
                        bg-gray-400 cursor-not-allowed opacity-60
                    @endif"
                @if(!$hasNextStage) disabled @endif
            >
                Valider
            </button>

            <!-- Request Completion Button -->
            <button 
                @click="showCompletionForm = !showCompletionForm; showValidateForm = false; showRejectForm = false"
                type="button"
                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff5500]"
            >
                Demander un complément
            </button>

            <!-- Reject Button (only show after Business Submission stage) -->
            @if(!$isBusinessSubmissionStage)
            <button 
                @click="showRejectForm = !showRejectForm; showValidateForm = false; showCompletionForm = false"
                type="button"
                class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
                Rejeter
            </button>
            @endif
        </div>

        <!-- Validate Form -->
        <div x-show="showValidateForm" x-cloak class="mt-4 border-t border-gray-200 pt-4">
            <form method="POST" action="{{ route('saisines.validate', $saisine) }}">
                @csrf
                
                @if($isDRJStudyStage)
                <!-- DRJ-specific validation options -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de validation</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="action" 
                                value="validate" 
                                x-model="validationAction"
                                class="h-4 w-4 text-[#ff5500] focus:ring-[#ff5500] border-gray-300"
                                required
                            >
                            <span class="ml-2 text-sm text-gray-700">Validation standard</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="action" 
                                value="partial_validation" 
                                x-model="validationAction"
                                class="h-4 w-4 text-[#ff5500] focus:ring-[#ff5500] border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700">Validation partielle</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="action" 
                                value="total_validation" 
                                x-model="validationAction"
                                class="h-4 w-4 text-[#ff5500] focus:ring-[#ff5500] border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700">Validation totale</span>
                        </label>
                    </div>
                </div>
                @else
                <!-- Standard validation for other stages -->
                <input type="hidden" name="action" value="validate">
                @endif

                <!-- Comment textarea -->
                <div class="mb-4">
                    <label for="validation-comment" class="block text-sm font-medium text-gray-700 mb-2">
                        Commentaire <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="validation-comment"
                        name="comment" 
                        rows="4" 
                        x-model="validationComment"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff5500] focus:border-[#ff5500]"
                        placeholder="Entrez votre commentaire de validation..."
                        required
                    ></textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="showValidateForm = false; validationComment = ''"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#ff5500] rounded-md hover:bg-[#e64d00] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff5500]"
                    >
                        Soumettre la validation
                    </button>
                </div>
            </form>
        </div>

        <!-- Request Completion Form -->
        <div x-show="showCompletionForm" x-cloak class="mt-4 border-t border-gray-200 pt-4">
            <form method="POST" action="{{ route('saisines.request-completion', $saisine) }}">
                @csrf
                
                <!-- Reason textarea -->
                <div class="mb-4">
                    <label for="completion-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de la demande de complément <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="completion-reason"
                        name="reason" 
                        rows="4" 
                        x-model="completionReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff5500] focus:border-[#ff5500]"
                        placeholder="Expliquez quelles informations supplémentaires ou modifications sont nécessaires..."
                        required
                    ></textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="showCompletionForm = false; completionReason = ''"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Demander un complément
                    </button>
                </div>
            </form>
        </div>

        <!-- Reject Form -->
        <div x-show="showRejectForm" x-cloak class="mt-4 border-t border-gray-200 pt-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-900 mb-1">Attention</h3>
                        <p class="text-sm text-red-700">
                            Cette action renverra la demande à l'étape "Qualification RJG" pour réévaluation.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('saisines.reject', $saisine) }}">
                @csrf
                
                <!-- Reason textarea -->
                <div class="mb-4">
                    <label for="reject-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du rejet <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="reject-reason"
                        name="reason" 
                        rows="4" 
                        x-model="rejectReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        placeholder="Expliquez pourquoi cette demande est rejetée et doit retourner à la qualification RJG..."
                        required
                    ></textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="showRejectForm = false; rejectReason = ''"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endif
