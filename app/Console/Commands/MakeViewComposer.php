<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeViewComposer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:make-view-composer';
    protected $signature = 'make:view-composer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo View Composer en la carpeta app/View/Composers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $composerPath = app_path("View/Composers/{$name}.php");

        if (File::exists($composerPath)) {
            $this->error("El View Composer {$name} ya existe.");
            return;
        }

        // Asegurarse de que la carpeta View/Composers existe
        File::ensureDirectoryExists(app_path('View/Composers'));

        // Plantilla del View Composer
        $stub = <<<PHP
        <?php

        namespace App\View\Composers;

        use Illuminate\View\View;

        class {$name}
        {
            public function compose(View \$view)
            {
                // Aquí puedes cargar los datos y pasarlos a la vista
                \$view->with('data', []);
            }
        }
        PHP;

        File::put($composerPath, $stub);

        $this->info("View Composer {$name} creado en app/View/Composers/");
    }
}
