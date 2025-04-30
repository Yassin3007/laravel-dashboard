<?php

namespace App\Setup;

use Illuminate\Support\Facades\File;

class SetupFolders
{
    /**
     * Create the necessary folders for the CRUD generator
     *
     * @return void
     */
    public static function createFolders()
    {
        // Create stubs directory
        if (!File::exists(app_path('Console/Commands/stubs'))) {
            File::makeDirectory(app_path('Console/Commands/stubs'), 0755, true);
        }

        // Create views stubs directory
        if (!File::exists(app_path('Console/Commands/stubs/views'))) {
            File::makeDirectory(app_path('Console/Commands/stubs/views'), 0755, true);
        }

        // Create templates directory in views
        if (!File::exists(resource_path('views/templates'))) {
            File::makeDirectory(resource_path('views/templates'), 0755, true);
        }

        // Create layouts directory in views if not exists
        if (!File::exists(resource_path('views/layouts'))) {
            File::makeDirectory(resource_path('views/layouts'), 0755, true);
        }

        echo "Folder structure created successfully!\n";
    }
}
