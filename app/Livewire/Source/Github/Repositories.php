<?php

namespace App\Livewire\Source\Github;

use App\Models\GithubApp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Repositories extends Component
{
    use AuthorizesRequests;

    public ?GithubApp $github_app = null;

    public string $search = '';

    public $repositories = [];

    public int $totalCount = 0;

    public bool $loading = true;

    public ?string $error = null;

    public function mount(string $github_app_uuid): void
    {
        $this->github_app = GithubApp::ownedByCurrentTeam()
            ->whereUuid($github_app_uuid)
            ->firstOrFail();

        $this->authorize('view', $this->github_app);
        $this->loadRepositories();
    }

    public function loadRepositories(): void
    {
        $this->loading = true;
        $this->error = null;

        try {
            $token = generateGithubInstallationToken($this->github_app);
            $page = 1;
            $all = collect();

            $result = loadRepositoryByPage($this->github_app, $token, $page);
            $this->totalCount = $result['total_count'];
            $all = $all->concat($result['repositories']);

            while ($all->count() < $this->totalCount) {
                $page++;
                $result = loadRepositoryByPage($this->github_app, $token, $page);
                $this->totalCount = $result['total_count'];
                $all = $all->concat($result['repositories']);
            }

            $this->repositories = $all->sortBy('full_name')->values()->all();
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function getFilteredRepositoriesProperty(): array
    {
        if (blank($this->search)) {
            return $this->repositories;
        }

        $search = strtolower($this->search);

        return collect($this->repositories)
            ->filter(fn ($repo) => str_contains(strtolower(data_get($repo, 'full_name', '')), $search)
                || str_contains(strtolower(data_get($repo, 'description', '') ?? ''), $search))
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.source.github.repositories');
    }
}
