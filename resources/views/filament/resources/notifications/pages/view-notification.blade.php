@php
    use App\Models\Notification;
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Status Badge -->
        <div class="flex items-center gap-3">
            @if ($this->record->isRead())
                <x-filament::badge color="gray">Read</x-filament::badge>
            @else
                <x-filament::badge color="warning">Unread</x-filament::badge>
            @endif
        </div>

        <!-- Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $this->record->getTitle() }}
            </h2>
        </div>

        <!-- Message Body -->
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! nl2br(e($this->record->getBody())) !!}
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Type -->
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Type</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $this->record->getCategory() }}
                </p>
            </div>

            <!-- Received At -->
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Received At</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $this->record->created_at->format('d M Y, H:i:s') }}
                </p>
            </div>

            <!-- Read At -->
            @if ($this->record->read_at)
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Read At</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $this->record->read_at->format('d M Y, H:i:s') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
