@props(['unreadCount' => 0, 'notifications' => []])

<div x-data="{ open: false }" class="relative">
    <!-- Notification Bell Button -->
    <button @click="open = !open" type="button" class="relative p-2 text-gray-500 hover:text-[--color-orange-primary] focus:outline-none transition-colors">
        <!-- Bell Icon -->
        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Count Badge -->
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-[--color-orange-primary] rounded-full">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                @csrf
                <button type="submit" class="text-xs text-[--color-orange-primary] hover:text-[--color-orange-dark] transition-colors">
                    Tout marquer comme lu
                </button>
            </form>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
            <a href="{{ route('notifications.show', $notification->id) }}" 
               class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors {{ is_null($notification->read_at) ? 'bg-orange-50' : '' }}">
                <div class="flex items-start">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 {{ is_null($notification->read_at) ? 'font-semibold' : '' }} truncate">
                            {{ $notification->data['message'] ?? 'Nouvelle notification' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(is_null($notification->read_at))
                    <span class="ml-2 flex-shrink-0 bg-[--color-orange-primary] rounded-full icon-sm"></span>
                    @endif
                </div>
            </a>
            @empty
            <div class="px-4 py-8 text-center text-gray-500 text-sm">
                Aucune notification
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
        <div class="px-4 py-3 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-[--color-orange-primary] hover:text-[--color-orange-dark] transition-colors">
                Voir toutes les notifications
            </a>
        </div>
        @endif
    </div>
</div>
