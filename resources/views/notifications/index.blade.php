@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        
        @if(Auth::user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-[#ff5500] text-white rounded hover:bg-[#cc4400] transition-colors">
                Tout marquer comme lu
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @forelse($notifications as $notification)
        <div class="border-b border-gray-200 {{ is_null($notification->read_at) ? 'bg-orange-50' : 'bg-white' }}">
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <p class="text-sm font-medium text-gray-900 {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                                {{ $notification->data['message'] ?? 'Nouvelle notification' }}
                            </p>
                            @if(is_null($notification->read_at))
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#ff5500] text-white">
                                Nouveau
                            </span>
                            @endif
                        </div>
                        
                        @if(isset($notification->data['reference_number']))
                        <p class="mt-1 text-sm text-gray-600">
                            Référence : <span class="font-medium">{{ $notification->data['reference_number'] }}</span>
                        </p>
                        @endif
                        
                        @if(isset($notification->data['title']))
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $notification->data['title'] }}
                        </p>
                        @endif
                        
                        @if(isset($notification->data['reason']))
                        <p class="mt-2 text-sm text-gray-700 italic">
                            "{{ $notification->data['reason'] }}"
                        </p>
                        @endif
                        
                        <p class="mt-2 text-xs text-gray-500">
                            {{ $notification->created_at->format('d/m/Y à H:i') }} ({{ $notification->created_at->diffForHumans() }})
                        </p>
                    </div>
                    
                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                        @if(isset($notification->data['saisine_id']))
                        <a href="{{ route('notifications.show', $notification->id) }}" 
                           class="inline-flex items-center px-3 py-2 border border-[#ff5500] text-sm font-medium rounded text-[#ff5500] bg-white hover:bg-orange-50 transition-colors">
                            Voir la demande
                        </a>
                        @endif
                        
                        @if(is_null($notification->read_at))
                        <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Marquer comme lu
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto text-gray-400" style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Aucune notification</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
