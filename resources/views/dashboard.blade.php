@extends('layouts.app')

@section('title', 'Tableau de bord - Saisine')

@section('content')
<div class="p-8">
    <!-- Header -->
    <div class="mb-2">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
    </div> 

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="icon-md text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filtres
        </h2>
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-4">
            <!-- Stage Filter -->
            <div class="flex-1" x-data="{ 
                open: false, 
                selected: '{{ request('stage') }}', 
                selectedText: '{{ request('stage') ? $stages->firstWhere('id', request('stage'))->display_name : 'Toutes les étapes' }}'
            }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Étape</label>
                <div class="relative">
                    <button 
                        type="button"
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-4 py-3.5 text-left bg-white border border-gray-300 rounded-xl shadow-sm hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 flex items-center justify-between"
                    >
                        <span x-text="selectedText" class="text-gray-900"></span>
                        <svg class="icon-sm text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto"
                        style="display: none;"
                    >
                        <div 
                            @click="selected = ''; selectedText = 'Toutes les étapes'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '' }"
                        >
                            <span class="text-gray-900">Toutes les étapes</span>
                            <svg x-show="selected === ''" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @foreach($stages as $stage)
                        <div 
                            @click="selected = '{{ $stage->id }}'; selectedText = '{{ $stage->display_name }}'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '{{ $stage->id }}' }"
                        >
                            <span class="text-gray-900">{{ $stage->display_name }}</span>
                            <svg x-show="selected === '{{ $stage->id }}'" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="stage" :value="selected">
                </div>
            </div>

            <!-- Urgency Filter -->
            <div class="flex-1" x-data="{ 
                open: false, 
                selected: '{{ request('urgency') }}', 
                selectedText: '{{ request('urgency') ? (request('urgency') === 'critical' ? 'Critique' : (request('urgency') === 'priority' ? 'Prioritaire' : 'Standard')) : 'Toutes les urgences' }}'
            }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Urgence</label>
                <div class="relative">
                    <button 
                        type="button"
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-4 py-3.5 text-left bg-white border border-gray-300 rounded-xl shadow-sm hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 flex items-center justify-between"
                    >
                        <span x-text="selectedText" class="text-gray-900"></span>
                        <svg class="icon-sm text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg"
                        style="display: none;"
                    >
                        <div 
                            @click="selected = ''; selectedText = 'Toutes les urgences'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '' }"
                        >
                            <span class="text-gray-900">Toutes les urgences</span>
                            <svg x-show="selected === ''" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @foreach($urgencyLevels as $level)
                        <div 
                            @click="selected = '{{ $level }}'; selectedText = '{{ $level === 'critical' ? 'Critique' : ($level === 'priority' ? 'Prioritaire' : 'Standard') }}'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '{{ $level }}' }"
                        >
                            <span class="text-gray-900">
                                @if($level === 'critical') Critique
                                @elseif($level === 'priority') Prioritaire
                                @else Standard
                                @endif
                            </span>
                            <svg x-show="selected === '{{ $level }}'" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="urgency" :value="selected">
                </div>
            </div>

            <!-- Request Type Filter -->
            <div class="flex-1" x-data="{ 
                open: false, 
                selected: '{{ request('request_type') }}', 
                selectedText: '{{ request('request_type') ? $requestTypes->firstWhere('id', request('request_type'))->display_name : 'Tous les types' }}'
            }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de demande</label>
                <div class="relative">
                    <button 
                        type="button"
                        @click="open = !open"
                        @click.away="open = false"
                        class="w-full px-4 py-3.5 text-left bg-white border border-gray-300 rounded-xl shadow-sm hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 flex items-center justify-between"
                    >
                        <span x-text="selectedText" class="text-gray-900"></span>
                        <svg class="icon-sm text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto"
                        style="display: none;"
                    >
                        <div 
                            @click="selected = ''; selectedText = 'Tous les types'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '' }"
                        >
                            <span class="text-gray-900">Tous les types</span>
                            <svg x-show="selected === ''" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @foreach($requestTypes as $type)
                        <div 
                            @click="selected = '{{ $type->id }}'; selectedText = '{{ $type->display_name }}'; open = false"
                            class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors duration-150 flex items-center justify-between"
                            :class="{ 'bg-orange-50': selected === '{{ $type->id }}' }"
                        >
                            <span class="text-gray-900">{{ $type->display_name }}</span>
                            <svg x-show="selected === '{{ $type->id }}'" class="icon-sm text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="request_type" :value="selected">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                @if(request('stage') || request('urgency') || request('request_type'))
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="px-5 py-3.5 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 flex items-center gap-2"
                    >
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Réinitialiser
                    </a>
                @endif
                <button 
                    type="submit" 
                    class="px-6 py-3.5 text-sm font-medium text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
                    style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                >
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Appliquer
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Demandes Card -->
        <div class="relative bg-white rounded-xl shadow-lg p-6 border-l-[6px] border-blue-500 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500 mb-1 font-medium">Total des demandes</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $allSaisines->count() }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl hover:scale-110 transition-transform duration-300">
                    <svg class="icon-lg text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Critiques Card -->
        <div class="relative bg-white rounded-xl shadow-lg p-6 border-l-[6px] border-red-500 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500 mb-1 font-medium">Critiques</div>
                    <div class="text-3xl font-bold text-red-600">{{ $allSaisines->where('urgency_level', 'critical')->count() }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-red-100 to-red-200 rounded-xl hover:scale-110 transition-transform duration-300">
                    <svg class="icon-lg text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Prioritaires Card -->
        <div class="relative bg-white rounded-xl shadow-lg p-6 border-l-[6px] border-orange-500 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500 mb-1 font-medium">Prioritaires</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $allSaisines->where('urgency_level', 'priority')->count() }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl hover:scale-110 transition-transform duration-300">
                    <svg class="icon-lg text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- En retard Card -->
        <div class="relative bg-white rounded-xl shadow-lg p-6 border-l-[6px] border-purple-500 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500 mb-1 font-medium">En retard</div>
                    <div class="text-3xl font-bold text-red-600">{{ $allSaisines->where('is_delayed', true)->count() }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl hover:scale-110 transition-transform duration-300">
                    <svg class="icon-lg text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne 1: Progression dans le workflow (pleine largeur) -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
            Progression dans le workflow
        </h3>
        <canvas id="progressChart" style="max-height: 300px;"></canvas>
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <div class="text-sm text-gray-600 mb-2">Début du processus</div>
                <div class="text-2xl font-bold text-blue-600">
                    {{ $allSaisines->filter(function($s) use ($stages) { 
                        return $s->current_stage_id <= $stages->get(2)->id; 
                    })->count() }}
                </div>
            </div>
            <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                <div class="text-sm text-gray-600 mb-2">En cours de traitement</div>
                <div class="text-2xl font-bold text-orange-600">
                    {{ $allSaisines->filter(function($s) use ($stages) { 
                        return $s->current_stage_id > $stages->get(2)->id && $s->current_stage_id < $stages->last()->id; 
                    })->count() }}
                </div>
            </div>
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <div class="text-sm text-gray-600 mb-2">Demandes finalisées</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ $allSaisines->where('current_stage_id', $stages->last()->id)->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne 2: Répartition par urgence + Répartition par étape -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Urgency Distribution Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
                Répartition par urgence
            </h3>
            <canvas id="urgencyChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Stage Distribution Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
                Répartition par étape
            </h3>
            <canvas id="stageChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Ligne 3: Taux de complétion + Délais de traitement -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Workflow Completion Rate -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div>
                Taux de complétion du workflow
            </h3>
            <canvas id="completionChart" style="max-height: 300px;"></canvas>
            <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Demandes terminées</span>
                    <span class="text-2xl font-bold text-green-600">
                        @php
                            $lastStage = $stages->last();
                            $completedCount = $allSaisines->where('current_stage_id', $lastStage->id)->count();
                            $totalCount = $allSaisines->count();
                            $percentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;
                        @endphp
                        {{ $percentage }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Délais de traitement -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-indigo-500 to-indigo-600 rounded-full"></div>
                Délais de traitement
            </h3>
            <canvas id="delayChart" style="max-height: 300px;"></canvas>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                    <div class="text-xs text-gray-600 mb-1">Dans les délais</div>
                    <div class="text-xl font-bold text-green-600">
                        {{ $allSaisines->where('is_delayed', false)->count() }}
                    </div>
                </div>
                <div class="p-3 bg-gradient-to-br from-red-50 to-red-100 rounded-lg border border-red-200">
                    <div class="text-xs text-gray-600 mb-1">En retard</div>
                    <div class="text-xl font-bold text-red-600">
                        {{ $allSaisines->where('is_delayed', true)->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne 4: Demandes par type (pleine largeur) -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
            Demandes par type
        </h3>
        <canvas id="requestTypeChart" style="max-height: 300px;"></canvas>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

document.addEventListener('DOMContentLoaded', function() {
    // Urgency Chart
    const urgencyCtx = document.getElementById('urgencyChart');
    if (urgencyCtx) {
        new Chart(urgencyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Critique', 'Prioritaire', 'Standard'],
                datasets: [{
                    data: [
                        {{ $allSaisines->where('urgency_level', 'critical')->count() }},
                        {{ $allSaisines->where('urgency_level', 'priority')->count() }},
                        {{ $allSaisines->where('urgency_level', 'standard')->count() }}
                    ],
                    backgroundColor: ['#ef4444', '#ff5500', '#9ca3af']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Stage Chart
    const stageCtx = document.getElementById('stageChart');
    if (stageCtx) {
        new Chart(stageCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($stages as $stage)
                        '{{ $stage->display_name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Demandes',
                    data: [
                        @foreach($stages as $stage)
                            {{ $allSaisines->where('current_stage_id', $stage->id)->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#ff5500'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Request Type Chart
    const requestTypeCtx = document.getElementById('requestTypeChart');
    if (requestTypeCtx) {
        new Chart(requestTypeCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($requestTypes as $type)
                        '{{ $type->display_name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Demandes',
                    data: [
                        @foreach($requestTypes as $type)
                            {{ $allSaisines->where('request_type_id', $type->id)->count() }},
                        @endforeach
                    ],
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Workflow Completion Rate Chart
    const completionCtx = document.getElementById('completionChart');
    if (completionCtx) {
        const lastStageId = {{ $stages->last()->id }};
        const completedCount = {{ $allSaisines->where('current_stage_id', $stages->last()->id)->count() }};
        const inProgressCount = {{ $allSaisines->where('current_stage_id', '!=', $stages->last()->id)->count() }};
        
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Terminées', 'En cours'],
                datasets: [{
                    data: [completedCount, inProgressCount],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Workflow Progress Chart
    const progressCtx = document.getElementById('progressChart');
    if (progressCtx) {
        new Chart(progressCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($stages as $stage)
                        '{{ $stage->display_name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Nombre de demandes',
                    data: [
                        @foreach($stages as $stage)
                            {{ $allSaisines->where('current_stage_id', $stage->id)->count() }},
                        @endforeach
                    ],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Delay Chart
    const delayCtx = document.getElementById('delayChart');
    if (delayCtx) {
        new Chart(delayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dans les délais', 'En retard'],
                datasets: [{
                    data: [
                        {{ $allSaisines->where('is_delayed', false)->count() }},
                        {{ $allSaisines->where('is_delayed', true)->count() }}
                    ],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
