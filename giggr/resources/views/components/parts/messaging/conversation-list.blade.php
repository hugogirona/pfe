@props(['conversations', 'activeTab', 'currentUserId'])

<div
    id="messaging-inbox-panel"
    role="tabpanel"
    aria-labelledby="messaging-tab-{{ $activeTab }}"
    class="flex-1 overflow-y-auto"
>
    @if ($conversations->isEmpty())
        <div class="h-full flex flex-col items-center justify-center px-8 py-12 text-center" role="status">
            <div class="w-16 h-16 rounded-full bg-dark/5 flex items-center justify-center mb-4" aria-hidden="true">
                <x-icon name="chat-bubble" class="w-8 h-8 text-dark/30"/>
            </div>
            <p class="text-sm text-dark/50 italic">
                {{ $activeTab === 'messages' ? __('messaging.empty_messages') : __('messaging.empty_requests') }}
            </p>
        </div>
    @else
        <ul class="divide-y divide-dark/8">
            @foreach ($conversations as $conversation)
                <li>
                    <x-parts.messaging.conversation-row
                        :conversation="$conversation"
                        :current-user-id="$currentUserId"
                    />
                </li>
            @endforeach
        </ul>
    @endif
</div>
