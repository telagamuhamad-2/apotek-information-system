<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Remove 'Service' suffix if provided
        $serviceName = str_ends_with($name, 'Service') ? $name : $name . 'Service';

        $servicePath = app_path("Services/{$serviceName}.php");

        // Check if file already exists
        if (File::exists($servicePath)) {
            $this->error("Service {$serviceName} already exists!");
            return Command::FAILURE;
        }

        // Create Services directory if not exists
        if (!File::isDirectory(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }

        // Generate Service
        $serviceContent = $this->generateServiceContent($serviceName);

        File::put($servicePath, $serviceContent);

        $this->info("Service created successfully:");
        $this->line("  - {$servicePath}");

        return Command::SUCCESS;
    }

    private function generateServiceContent($serviceName): string
    {
        // Extract model name from service name (e.g., ProductService -> Product)
        $modelName = str_replace('Service', '', $serviceName);

        // Generate the service content
        return <<<PHP
<?php

namespace App\Services;

use App\Contracts\\{$modelName}RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class {$serviceName}
{
    protected \$repository;

    public function __construct({$modelName}RepositoryInterface \$repository)
    {
        \$this->repository = \$repository;
    }

    /**
     * Get all records with optional filters
     */
    public function getAll(array \$filters = []): Collection
    {
        try {
            return \$this->repository->all(\$filters);
        } catch (Exception \$e) {
            throw new Exception("Failed to retrieve records: " . \$e->getMessage());
        }
    }

    /**
     * Get paginated records with filters
     */
    public function getPaginated(int \$perPage = 10, array \$filters = []): LengthAwarePaginator
    {
        try {
            return \$this->repository->paginate(\$perPage, \$filters);
        } catch (Exception \$e) {
            throw new Exception("Failed to retrieve paginated records: " . \$e->getMessage());
        }
    }

    /**
     * Find a record by ID
     */
    public function findById(int \$id)
    {
        try {
            \$record = \$this->repository->find(\$id);
            if (!\$record) {
                throw new Exception("Record not found.");
            }
            return \$record;
        } catch (Exception \$e) {
            throw \$e;
        }
    }

    /**
     * Create a new record
     */
    public function create(array \$data)
    {
        try {
            DB::beginTransaction();
            \$record = \$this->repository->create(\$data);
            DB::commit();
            return \$record;
        } catch (Exception \$e) {
            DB::rollBack();
            throw new Exception("Failed to create record: " . \$e->getMessage());
        }
    }

    /**
     * Update an existing record
     */
    public function update(int \$id, array \$data)
    {
        try {
            DB::beginTransaction();
            \$record = \$this->repository->update(\$id, \$data);
            if (!\$record) {
                throw new Exception("Record not found.");
            }
            DB::commit();
            return \$record;
        } catch (Exception \$e) {
            DB::rollBack();
            throw new Exception("Failed to update record: " . \$e->getMessage());
        }
    }

    /**
     * Delete a record
     */
    public function delete(int \$id): bool
    {
        try {
            DB::beginTransaction();
            \$result = \$this->repository->delete(\$id);
            DB::commit();
            return \$result;
        } catch (Exception \$e) {
            DB::rollBack();
            throw new Exception("Failed to delete record: " . \$e->getMessage());
        }
    }

    /**
     * Search records
     */
    public function search(string \$query, array \$filters = []): Collection
    {
        try {
            return \$this->repository->search(\$query, \$filters);
        } catch (Exception \$e) {
            throw new Exception("Failed to search records: " . \$e->getMessage());
        }
    }
}
PHP;
    }
}
