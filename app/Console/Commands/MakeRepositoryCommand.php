<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $nameWithoutSuffix = str_replace('Repository', '', $name);

        $contractPath = app_path("Contracts/{$name}Interface.php");
        $repositoryPath = app_path("Repositories/{$name}.php");

        // Check if files already exist
        if (File::exists($contractPath) && File::exists($repositoryPath)) {
            $this->error('Repository and Interface already exist!');
            return Command::FAILURE;
        }

        // Create Contracts directory if not exists
        if (!File::isDirectory(app_path('Contracts'))) {
            File::makeDirectory(app_path('Contracts'), 0755, true);
        }

        // Create Repositories directory if not exists
        if (!File::isDirectory(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'), 0755, true);
        }

        // Generate Interface
        $contractContent = $this->generateContractContent($name);
        File::put($contractPath, $contractContent);

        // Generate Repository
        $repositoryContent = $this->generateRepositoryContent($name, $nameWithoutSuffix);
        File::put($repositoryPath, $repositoryContent);

        $this->info("Repository created successfully:");
        $this->line("  - {$contractPath}");
        $this->line("  - {$repositoryPath}");

        return Command::SUCCESS;
    }

    private function generateContractContent($name): string
    {
        return <<<PHP
<?php

namespace App\Contracts;

interface {$name}Interface
{
    public function all(array \$filters = []);
    public function find(int \$id);
    public function create(array \$data);
    public function update(int \$id, array \$data);
    public function delete(int \$id);
    public function paginate(int \$perPage = 10, array \$filters = []);
    public function search(string \$query, array \$filters = []);
}
PHP;
    }

    private function generateRepositoryContent($name, $nameWithoutSuffix): string
    {
        // Handle case where name already includes "Repository"
        $modelName = str_replace('Repository', '', $name);

        // Import statements
        $imports = [];
        $imports[] = 'use App\Contracts\\' . $name . 'Interface';
        $imports[] = 'use App\\Models\\' . $modelName;
        $imports[] = 'use Illuminate\\Pagination\\LengthAwarePaginator';
        $imports[] = 'use Illuminate\\Database\\Eloquent\\Collection';

        $importContent = implode("\n", $imports) . "\n";

        return <<<PHP
<?php

{$importContent}

class {$name} implements {$name}Interface
{
    protected \$model;
    protected \$perPage = 10;

    public function __construct({$modelName} \$model)
    {
        \$this->model = \$model;
    }

    public function all(array \$filters = []): Collection
    {
        \$query = \$this->model->query();

        \$this->applyFilters(\$query, \$filters);

        return \$query->get();
    }

    public function find(int \$id)
    {
        return \$this->model->find(\$id);
    }

    public function create(array \$data)
    {
        return \$this->model->create(\$data);
    }

    public function update(int \$id, array \$data)
    {
        \$record = \$this->find(\$id);
        if (!\$record) {
            return null;
        }

        \$record->update(\$data);
        return \$record->fresh();
    }

    public function delete(int \$id): bool
    {
        \$record = \$this->find(\$id);
        if (!\$record) {
            return false;
        }

        return \$record->delete();
    }

    public function paginate(int \$perPage = 10, array \$filters = []): LengthAwarePaginator
    {
        \$query = \$this->model->query();

        \$this->applyFilters(\$query, \$filters);

        \$this->perPage = \$perPage;

        return \$query->orderBy('id', 'desc')->paginate(\$this->perPage);
    }

    public function search(string \$query, array \$filters = []): Collection
    {
        \$searchQuery = \$this->model->query();

        \$this->applyFilters(\$searchQuery, \$filters);

        return \$searchQuery->get();
    }

    protected function applyFilters(\$query, array \$filters): void
    {
        foreach (\$filters as \$field => \$value) {
            if (\$value !== null && \$value !== '') {
                if (in_array(\$field, \$this->model->getFillable())) {
                    \$query->where(\$field, 'LIKE', "%{\$value}%");
                }
            }
        }
    }
}
PHP;
    }
}
