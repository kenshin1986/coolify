<x-layout>
    <x-slot:title>
        Repositories | {{ $github_app->name }} | Coolify
    </x-slot>
    <div class="flex items-center gap-2">
        <a href="{{ route('source.github.show', ['github_app_uuid' => $github_app->uuid]) }}"
            class="text-sm opacity-60 hover:opacity-100">
            {{ $github_app->name }}
        </a>
        <span class="opacity-40">/</span>
        <h1>Repositories</h1>
    </div>
    <div class="subtitle">
        Repositories accessible via this GitHub App.
    </div>

    @if ($error)
        <div class="mb-4 rounded-sm alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($loading)
        <div class="flex items-center gap-2 text-sm opacity-60">
            <x-loading />
            <span>Loading repositories…</span>
        </div>
    @else
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-4">
            <x-forms.input wire:model.live.debounce.300ms="search" placeholder="Search repositories…"
                class="w-full sm:w-72" />
            <span class="text-sm opacity-60 whitespace-nowrap">
                {{ count($this->filteredRepositories) }} / {{ $totalCount }}
                {{ Str::plural('repository', $totalCount) }}
            </span>
        </div>

        @if (empty($this->filteredRepositories))
            <div class="py-6 text-sm opacity-60">
                @if ($totalCount === 0)
                    No repositories found. Make sure the GitHub App has access to at least one repository.
                @else
                    No repositories match your search.
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-coolgray-200">
                            <th class="py-2 pr-4 text-left font-medium opacity-60">Repository</th>
                            <th class="py-2 pr-4 text-left font-medium opacity-60">Default Branch</th>
                            <th class="py-2 pr-4 text-left font-medium opacity-60">Description</th>
                            <th class="py-2 text-left font-medium opacity-60">Visibility</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-coolgray-200">
                        @foreach ($this->filteredRepositories as $repo)
                            <tr class="hover:bg-coolgray-100/30 transition-colors">
                                <td class="py-3 pr-4">
                                    <a href="{{ data_get($repo, 'html_url') }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium hover:underline flex items-center gap-1">
                                        {{ data_get($repo, 'full_name') }}
                                        <x-external-link class="w-3 h-3 opacity-50" />
                                    </a>
                                </td>
                                <td class="py-3 pr-4 opacity-70 font-mono text-xs">
                                    {{ data_get($repo, 'default_branch', '—') }}
                                </td>
                                <td class="py-3 pr-4 opacity-60 max-w-xs truncate">
                                    {{ data_get($repo, 'description') ?: '—' }}
                                </td>
                                <td class="py-3">
                                    @if (data_get($repo, 'private'))
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning/20 text-warning">
                                            Private
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success/20 text-success">
                                            Public
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</x-layout>
