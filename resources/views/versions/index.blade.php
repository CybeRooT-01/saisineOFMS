@extends('layouts.app')

@section('title', 'Historique des versions - ' . $saisine->reference_number)

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Historique des versions</h1>
                    <p class="mt-2 text-gray-600">{{ $saisine->reference_number }} - {{ $saisine->title }}</p>
                </div>
                <a href="{{ route('saisines.show', $saisine) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-md transition duration-150">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à la demande
                </a>
            </div>
        </div>

        @if($versions->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune version trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">L'historique des versions apparaîtra ici au fur et à mesure des modifications.</p>
            </div>
        @else
            <!-- Version Comparison Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Comparer les versions
                </h2>
                <form method="GET" action="{{ route('saisines.versions.compare', $saisine) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="version1" class="block text-sm font-medium text-gray-700 mb-2">Version 1</label>
                        <select name="version1" id="version1" required class="w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl shadow-sm hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                            <option value="">Sélectionner une version...</option>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}">
                                    v{{ $version->version_number }} - {{ $version->created_at->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="version2" class="block text-sm font-medium text-gray-700 mb-2">Version 2</label>
                        <select name="version2" id="version2" required class="w-full px-4 py-3.5 text-gray-900 bg-white border border-gray-300 rounded-xl shadow-sm hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                            <option value="">Sélectionner une version...</option>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}">
                                    v{{ $version->version_number }} - {{ $version->created_at->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Comparer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Version List -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-semibold text-gray-900">Toutes les versions ({{ $versions->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($versions as $version)
                        <div class="px-6 py-4 hover:bg-orange-50 transition-all duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Version {{ $version->version_number }}
                                        </span>
                                        @if($loop->first)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Actuelle
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-900 font-medium">{{ $version->change_description }}</p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $version->author->name }}
                                        <span class="mx-2">•</span>
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $version->created_at->format('d/m/Y H:i') }}
                                        <span class="mx-2">•</span>
                                        {{ $version->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('saisines.versions.show', [$saisine, $version]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-orange-50 hover:border-orange-300 transition-all duration-150 shadow-sm">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Voir le snapshot
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
