<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (file_exists($path)) {
            $this->error('Service already exists!');
            return;
        }

        file_put_contents($path, "<?php

namespace App\Services;

class {$name}
{
}
");

        $this->info("Service {$name} created successfully.");
    }
}
