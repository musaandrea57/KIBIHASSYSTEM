<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="{{ route('principal.communication.index') }}" 
           class="{{ request()->routeIs('principal.communication.index') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-inbox mr-2"></i> Inbox
        </a>

        <a href="{{ route('principal.communication.sent') }}" 
           class="{{ request()->routeIs('principal.communication.sent') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-paper-plane mr-2"></i> Sent Official Messages
        </a>

        <a href="{{ route('principal.communication.announcements') }}" 
           class="{{ request()->routeIs('principal.communication.announcements*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-bullhorn mr-2"></i> Institutional Announcements
        </a>

        <a href="{{ route('principal.communication.create') }}" 
           class="{{ request()->routeIs('principal.communication.create') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ml-auto text-indigo-600">
            <i class="fas fa-pen-nib mr-2"></i> Compose Official Message
        </a>
    </nav>
</div>
