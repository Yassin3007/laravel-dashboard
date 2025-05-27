<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CrudGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
                            {name : The name of the CRUD module (singular)}
                            {--fields= : The fields for the model and migration}
                            {--validation= : Validation rules for the fields}
                            {--relationships= : Relationships for the model}
                            {--with-resource : Create resource controller and routes}
                            {--with-permissions : Add permissions for the CRUD module}';


    // php artisan make:crud Post --fields=title:string,content:text,published:boolean --validation='{"title":"required|max:255","content":"required"}' --relationships='{"belongsTo":["User"]}' --with-resource

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CRUD module including model, controller, views, migration, and routes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the model name from the argument
        $name = $this->argument('name');
        $modelName = Str::studly(Str::singular($name));
        $tableName = Str::plural(Str::snake($name));

        // Get the fields from the option
        $fields = $this->option('fields')
            ? explode(',', $this->option('fields'))
            : [];

        // Get the validation rules
        $validationRules = $this->option('validation')
            ? json_decode($this->option('validation'), true)
            : [];

        // Get the relationships
        $relationships = $this->option('relationships')
            ? json_decode($this->option('relationships'), true)
            : [];

        // Check if we need to create resource controller and routes
        $withResource = $this->option('with-resource');

        // Check if we need to add permissions
//        $withPermissions = $this->option('with-permissions');
        $withPermissions = true;

        // Begin the process
        $this->info('Creating CRUD for: ' . $modelName);

        // Create each component
        $this->createModel($modelName, $tableName, $fields, $relationships);
        $this->createMigration($tableName, $fields);
        $this->createController($modelName, $withResource);
        $this->createViews($modelName, $tableName, $fields);
        $this->updateRoutes($modelName, $tableName, $withResource);
        $this->createFormRequest($modelName, $validationRules);

        if ($withPermissions) {
            $this->addPermissions($modelName);
        }

        // Add sidebar item
        $this->addSidebarItem($modelName, $tableName);

        $this->info('CRUD for ' . $modelName . ' created successfully!');

        return 0;
    }

    /**
     * Create the model.
     *
     * @param string $modelName
     * @param array $fields
     * @param array $relationships
     * @return void
     */
    protected function createModel($modelName, $tableName, $fields, $relationships)
    {
        $this->info('Creating Model: ' . $modelName);

        $modelTemplate = File::get(app_path('Console/Commands/stubs/model.stub'));

        // Prepare fillable fields
        $fillable = [];
        foreach ($fields as $field) {
            $fieldName = trim(explode(':', $field)[0]);
            if ($fieldName !== 'id') {
                $fillable[] = "'" . $fieldName . "'";
            }
        }

        // Prepare relationships
        $relationshipMethods = [];
        foreach ($relationships as $relation => $details) {
            switch ($relation) {
                case 'belongsTo':
                    foreach ($details as $relatedModel) {
                        $relationshipMethods[] = $this->generateBelongsToRelationship($relatedModel);
                    }
                    break;
                case 'hasMany':
                    foreach ($details as $relatedModel) {
                        $relationshipMethods[] = $this->generateHasManyRelationship($relatedModel);
                    }
                    break;
                case 'belongsToMany':
                    foreach ($details as $relatedModel) {
                        $relationshipMethods[] = $this->generateBelongsToManyRelationship($relatedModel);
                    }
                    break;
                case 'hasOne':
                    foreach ($details as $relatedModel) {
                        $relationshipMethods[] = $this->generateHasOneRelationship($relatedModel);
                    }
                    break;
            }
        }

        // Replace placeholders
        $modelTemplate = str_replace('{{modelName}}', $modelName, $modelTemplate);
        $modelTemplate = str_replace('{{tableName}}', $tableName, $modelTemplate);  // Add this line to replace table name
        $modelTemplate = str_replace('{{fillable}}', implode(', ', $fillable), $modelTemplate);
        $modelTemplate = str_replace('{{relationships}}', implode("\n\n    ", $relationshipMethods), $modelTemplate);

        // Create directories if needed
        if (!File::exists(app_path('Models'))) {
            File::makeDirectory(app_path('Models'));
        }

        // Save model file
        File::put(app_path('Models/' . $modelName . '.php'), $modelTemplate);
    }


    /**
     * Create the migration.
     *
     * @param string $tableName
     * @param array $fields
     * @return void
     */
    protected function createMigration($tableName, $fields)
    {
        $this->info('Creating Migration for table: ' . $tableName);

        // Generate migration file
        $migrationName = 'create_' . $tableName . '_table';
        Artisan::call('make:migration', [
            'name' => $migrationName
        ]);

        // Get the path of newly created migration
        $migrationPath = database_path('migrations/' . $this->getLatestMigration($migrationName));

        // Get migration content
        $migrationContent = File::get($migrationPath);

        // Generate schema fields
        $schemaFields = [];
        foreach ($fields as $field) {
            $fieldData = explode(':', $field);
            $fieldName = trim($fieldData[0]);
            $fieldType = isset($fieldData[1]) ? trim($fieldData[1]) : 'string';

            // Skip id field as it's added by default
            if ($fieldName === 'id') {
                continue;
            }

            // Handle special field types
            switch ($fieldType) {
                case 'string':
                    $schemaFields[] = "\$table->string('$fieldName');";
                    break;
                case 'text':
                    $schemaFields[] = "\$table->text('$fieldName');";
                    break;
                case 'integer':
                case 'int':
                    $schemaFields[] = "\$table->integer('$fieldName');";
                    break;
                case 'bigint':
                    $schemaFields[] = "\$table->bigInteger('$fieldName');";
                    break;
                case 'boolean':
                case 'bool':
                    $schemaFields[] = "\$table->boolean('$fieldName');";
                    break;
                case 'date':
                    $schemaFields[] = "\$table->date('$fieldName');";
                    break;
                case 'datetime':
                    $schemaFields[] = "\$table->dateTime('$fieldName');";
                    break;
                case 'decimal':
                    $parts = explode(',', $fieldName);
                    $precision = isset($parts[1]) ? $parts[1] : 8;
                    $scale = isset($parts[2]) ? $parts[2] : 2;
                    $schemaFields[] = "\$table->decimal('$fieldName', $precision, $scale);";
                    break;
                case 'float':
                    $schemaFields[] = "\$table->float('$fieldName');";
                    break;
                case 'json':
                    $schemaFields[] = "\$table->json('$fieldName');";
                    break;
                case 'foreignId':
                    $relatedTable = Str::plural(Str::snake(str_replace('_id', '', $fieldName)));
                    $schemaFields[] = "\$table->foreignId('$fieldName')->constrained('$relatedTable');";
                    break;
                default:
                    $schemaFields[] = "\$table->string('$fieldName');";
                    break;
            }
        }

        // Add timestamps to schema
        $schemaFields[] = "\$table->timestamps();";

        // Replace schema creation part in migration file
        $schemaUp = implode("\n            ", $schemaFields);
        $migrationContent = preg_replace(
            '/\$table->id\(\);(\s+)(\$table->timestamps\(\);)/',
            "\$table->id();\n            " . $schemaUp,
            $migrationContent
        );

        // Save updated migration file
        File::put($migrationPath, $migrationContent);
    }

    /**
     * Create the controller.
     *
     * @param string $modelName
     * @param bool $withResource
     * @return void
     */
    protected function createController($modelName, $withResource)
    {
        $this->info('Creating Controller: ' . $modelName . 'Controller');

        if ($withResource) {
            // Create resource controller
            $controllerTemplate = File::get(app_path('Console/Commands/stubs/controller.resource.stub'));
        } else {
            // Create standard controller
            $controllerTemplate = File::get(app_path('Console/Commands/stubs/controller.stub'));
        }

        // Replace placeholders
        $controllerTemplate = str_replace('{{modelName}}', $modelName, $controllerTemplate);
        $controllerTemplate = str_replace('{{modelNameSingularLowerCase}}', Str::camel($modelName), $controllerTemplate);
        $controllerTemplate = str_replace('{{modelNamePluralLowerCase}}', Str::camel(Str::plural($modelName)), $controllerTemplate);
        $controllerTemplate = str_replace('{{modelNamePlural}}', Str::plural($modelName), $controllerTemplate);
        $controllerTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $controllerTemplate);

        // Create directories if needed
        if (!File::exists(app_path('Http/Controllers/Dashboard'))) {
            File::makeDirectory(app_path('Http/Controllers/Dashboard'), 0755, true);
        }

        // Save controller file
        File::put(app_path('Http/Controllers/Dashboard/' . $modelName . 'Controller.php'), $controllerTemplate);

        // Create the service class
        $this->createService($modelName);
    }

    /**
     * Create the service class.
     *
     * @param string $modelName
     * @return void
     */
    protected function createService($modelName)
    {
        $this->info('Creating Service: ' . $modelName . 'Service');

        $serviceTemplate = File::get(app_path('Console/Commands/stubs/service.stub'));

        // Replace placeholders
        $serviceTemplate = str_replace('{{modelName}}', $modelName, $serviceTemplate);
        $serviceTemplate = str_replace('{{modelNameSingularLowerCase}}', Str::camel($modelName), $serviceTemplate);
        $serviceTemplate = str_replace('{{modelNamePluralLowerCase}}', Str::camel(Str::plural($modelName)), $serviceTemplate);

        // Create directories if needed
        if (!File::exists(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }

        // Save service file
        File::put(app_path('Services/' . $modelName . 'Service.php'), $serviceTemplate);
    }
    /**
     * Create the views.
     *
     * @param string $modelName
     * @param string $tableName
     * @param array $fields
     * @return void
     */
//    protected function createViews($modelName, $tableName, $fields)
//    {
//        $this->info('Creating Views for: ' . $modelName);
//
//        $viewPath = resource_path('views/dashboard/' . Str::kebab(Str::plural($modelName)));
//
//        if (!File::exists($viewPath)) {
//            File::makeDirectory($viewPath, 0755, true);
//        }
//
//        // Get column information from database if table exists
//        $databaseColumns = [];
//        try {
//            if (Schema::hasTable($tableName)) {
//                $databaseColumns = Schema::getColumnListing($tableName);
//            }
//        } catch (\Exception $e) {
//            $this->warn('Could not fetch table schema: ' . $e->getMessage());
//        }
//
//        // Merge manually provided fields with database columns
//        $columns = !empty($fields) ? $fields : $databaseColumns;
//
//        // Define form fields based on column types
//        $formFields = [];
//        $tableHeaders = [];
//        $tableRows = [];
//
//        foreach ($columns as $column) {
//            // Skip id, timestamps, and created_at/updated_at fields in forms
//            if (in_array($column, ['id', 'created_at', 'updated_at'])) {
//                continue;
//            }
//
//            // Parse field name and type
//            if (strpos($column, ':') !== false) {
//                list($column, $type) = explode(':', $column);
//            } else {
//                // Try to get type from DB if available
//                $type = 'string'; // Default type
//                if (!empty($databaseColumns)) {
//                    try {
//                        $columnType = DB::getSchemaBuilder()->getColumnType($tableName, $column);
//                        switch ($columnType) {
//                            case 'boolean':
//                                $type = 'boolean';
//                                break;
//                            case 'integer':
//                            case 'bigint':
//                                $type = 'integer';
//                                break;
//                            case 'datetime':
//                                $type = 'datetime';
//                                break;
//                            case 'date':
//                                $type = 'date';
//                                break;
//                            case 'text':
//                            case 'longtext':
//                                $type = 'text';
//                                break;
//                            case 'decimal':
//                            case 'float':
//                                $type = 'decimal';
//                                break;
//                            default:
//                                $type = 'string';
//                        }
//                    } catch (\Exception $e) {
//                        $this->warn('Could not determine column type: ' . $e->getMessage());
//                    }
//                }
//            }
//
//            $column = trim($column);
//            $type = trim($type);
//
//            // Generate form field based on type
//            $formFields[] = $this->generateFormField($column, $type, $modelName);
//
//            // Generate table header
//            $tableHeaders[] = '<th>' . Str::title(str_replace('_', ' ', $column)) . '</th>';
//
//            // Generate table row cell
//            $tableRows[] = $this->generateTableCell($column, $type);
//        }
//
//        // Create index view
//        $indexTemplate = $this->loadViewTemplate('index');
//        $indexTemplate = str_replace('{{modelName}}', $modelName, $indexTemplate);
//        $indexTemplate = str_replace('{{modelNamePlural}}', Str::plural($modelName), $indexTemplate);
//        $indexTemplate = str_replace('{{modelNamePluralLowerCase}}', Str::camel(Str::plural($modelName)), $indexTemplate);
//        $indexTemplate = str_replace('{{modelNameSingularLowerCase}}', Str::camel($modelName), $indexTemplate);
//        $indexTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $indexTemplate);
//        $indexTemplate = str_replace('{{tableHeaders}}', implode("\n                ", $tableHeaders), $indexTemplate);
//        $indexTemplate = str_replace('{{tableRows}}', implode("\n                ", $tableRows), $indexTemplate);
//
//        File::put($viewPath . '/index.blade.php', $indexTemplate);
//
//        // Create create view
//        $createTemplate = $this->loadViewTemplate('create');
//        $createTemplate = str_replace('{{modelName}}', $modelName, $createTemplate);
//        $createTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $createTemplate);
//        $createTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $createTemplate);
//        $createTemplate = str_replace('{{formFields}}', implode("\n                ", $formFields), $createTemplate);
//
//        File::put($viewPath . '/create.blade.php', $createTemplate);
//
//        // Create edit view
//        $editTemplate = $this->loadViewTemplate('edit');
//        $editTemplate = str_replace('{{modelName}}', $modelName, $editTemplate);
//        $editTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $editTemplate);
//        $editTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $editTemplate);
//        $editTemplate = str_replace('{{formFields}}', implode("\n                ", $formFields), $editTemplate);
//
//        File::put($viewPath . '/edit.blade.php', $editTemplate);
//
//        // Create show view
//        $showTemplate = $this->loadViewTemplate('show');
//        $showTemplate = str_replace('{{modelName}}', $modelName, $showTemplate);
//        $showTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $showTemplate);
//        $showTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $showTemplate);
//
//        // Generate detail fields
//        $detailFields = [];
//        foreach ($columns as $column) {
//            if ($column === 'id') continue;
//
//            if (strpos($column, ':') !== false) {
//                list($column, $type) = explode(':', $column);
//            }
//
//            $column = trim($column);
//            $detailFields[] = $this->generateDetailField($column);
//        }
//
//        $showTemplate = str_replace('{{detailFields}}', implode("\n                ", $detailFields), $showTemplate);
//
//        File::put($viewPath . '/show.blade.php', $showTemplate);
//    }

    /**
     * Create the form request class.
     *
     * @param string $modelName
     * @param array $validationRules
     * @return void
     */
    protected function createFormRequest($modelName, $validationRules)
    {
        $this->info('Creating Form Request: ' . $modelName . 'Request');

        $requestTemplate = File::get(app_path('Console/Commands/stubs/request.stub'));

        // Prepare validation rules
        $rules = [];
        foreach ($validationRules as $field => $rule) {
            $rules[] = "            '$field' => '$rule',";
        }

        // Replace placeholders
        $requestTemplate = str_replace('{{modelName}}', $modelName, $requestTemplate);
        $requestTemplate = str_replace('{{validationRules}}', implode("\n", $rules), $requestTemplate);

        // Create directories if needed
        if (!File::exists(app_path('Http/Requests'))) {
            File::makeDirectory(app_path('Http/Requests'), 0755, true);
        }

        // Save request file
        File::put(app_path('Http/Requests/' . $modelName . 'Request.php'), $requestTemplate);
    }

    /**
     * Update the routes file.
     *
     * @param string $modelName
     * @param string $tableName
     * @param bool $withResource
     * @return void
     */
    protected function updateRoutes($modelName, $tableName, $withResource)
    {
        $this->info('Updating Routes for: ' . $modelName);

        $routesFile = base_path('routes/web.php');
        $routesContent = File::get($routesFile);

        // Generate route code
        $controllerClass = $modelName . 'Controller';
        $routeName = Str::kebab(Str::plural($modelName));
        $modelNameLower = Str::lower($modelName);
        $modelNameCamel = Str::camel($modelName);

        // Always use individual routes with permissions (ignore $withResource flag)
        $newRoute = "// Routes for $modelName
Route::middleware(['auth'])->group(function() {
    Route::get('$routeName', [$controllerClass::class, 'index'])
        ->name('$routeName.index')
        ->middleware('can:view_$modelNameLower');

    Route::get('$routeName/create', [$controllerClass::class, 'create'])
        ->name('$routeName.create')
        ->middleware('can:create_$modelNameLower');

    Route::post('$routeName', [$controllerClass::class, 'store'])
        ->name('$routeName.store')
        ->middleware('can:create_$modelNameLower');

    Route::get('$routeName/{{$modelNameCamel}}', [$controllerClass::class, 'show'])
        ->name('$routeName.show')
        ->middleware('can:view_$modelNameLower');

    Route::get('$routeName/{{$modelNameCamel}}/edit', [$controllerClass::class, 'edit'])
        ->name('$routeName.edit')
        ->middleware('can:edit_$modelNameLower');

    Route::put('$routeName/{{$modelNameCamel}}', [$controllerClass::class, 'update'])
        ->name('$routeName.update')
        ->middleware('can:edit_$modelNameLower');

    Route::delete('$routeName/{{$modelNameCamel}}', [$controllerClass::class, 'destroy'])
        ->name('$routeName.destroy')
        ->middleware('can:delete_$modelNameLower');
});";

        // Check if we need to add use statement for the controller
        if (strpos($routesContent, 'use App\Http\Controllers\Dashboard\\' . $controllerClass) === false) {
            $useStatement = 'use App\Http\Controllers\Dashboard\\' . $controllerClass . ';';

            // Find the last use statement
            $lastUsePos = strrpos($routesContent, 'use ');
            if ($lastUsePos !== false) {
                $endOfLine = strpos($routesContent, ';', $lastUsePos);
                if ($endOfLine !== false) {
                    $routesContent = substr_replace($routesContent, ";\n" . $useStatement, $endOfLine, 1);
                }
            } else {
                // No use statements, add at the top
                $routesContent = $useStatement . "\n\n" . $routesContent;
            }
        }

        // Add route to the end
        $routesContent .= "\n\n" . $newRoute;

        // Save updated routes file
        File::put($routesFile, $routesContent);
    }
    /**
     * Add permissions for the CRUD module.
     *
     * @param string $modelName
     * @return void
     */
//    protected function addPermissions($modelName)
//    {
//        $this->info('Adding Permissions for: ' . $modelName);
//
//        // Check if Spatie Permission package is installed
//        if (!class_exists('\Spatie\Permission\Models\Permission')) {
//            $this->warn('Spatie Permission package is not installed. Skipping permission creation.');
//            return;
//        }
//
//        // Get the model name in lower case
//        $modelNameLower = Str::lower($modelName);
//
//        // Define permissions
//        $permissions = [
//            'view_' . $modelNameLower,
//            'create_' . $modelNameLower,
//            'edit_' . $modelNameLower,
//            'delete_' . $modelNameLower,
//        ];
//
//        // Create each permission
//        foreach ($permissions as $permission) {
//            if (!\Spatie\Permission\Models\Permission::where('name', $permission)->exists()) {
//                \Spatie\Permission\Models\Permission::create(['name' => $permission]);
//                $this->info('Permission created: ' . $permission);
//            } else {
//                $this->info('Permission already exists: ' . $permission);
//            }
//        }
//    }

    /**
     * Add sidebar item for the CRUD module.
     *
     * @param string $modelName
     * @param string $tableName
     * @return void
     */
    protected function addSidebarItem($modelName, $tableName)
    {
        $this->info('Adding Sidebar Item for: ' . $modelName);

        $sidebarFile = resource_path('views/dashboard/layouts/menu.blade.php');

        // Create sidebar file if it doesn't exist
        if (!File::exists($sidebarFile)) {
            File::put($sidebarFile, $this->loadViewTemplate('sidebar'));
        }

        $sidebarContent = File::get($sidebarFile);

        // Create sidebar item in the format that matches the template
        $routeName = Str::kebab(Str::plural($modelName));
        $modelNamePlural = Str::plural($modelName);
        $modelNameTitle = Str::title(str_replace('_', ' ', $modelNamePlural));
        $modelNameLower = Str::lower($modelName);

        // Format for the new menu item with permission check
        $sidebarItem = "
@can('view_$modelNameLower')
<li class=\" nav-item\"><a href=\"{{ route('$routeName.index') }}\"><i class=\"icon-list\"></i><span data-i18n=\"nav.$routeName.main\" class=\"menu-title\">{{__('dashboard.$modelNameLower.title_plural')}}</span></a>
</li>
@endcan";

        // Find a good place to insert the new menu item - after the last </li> tag
        $lastPos = strrpos($sidebarContent, '</li>@endcan');
        if ($lastPos !== false) {
            // Insert after the last </li> tag
            $sidebarContent = substr_replace($sidebarContent, "\n$sidebarItem", $lastPos + 5, 0);
        } else {
            // Fallback: Just append to the end
            $sidebarContent .= $sidebarItem;
        }

        // Save updated sidebar file
        File::put($sidebarFile, $sidebarContent);
    }

    /**
     * Generate belongsTo relationship method.
     *
     * @param string $relatedModel
     * @return string
     */
    protected function generateBelongsToRelationship($relatedModel)
    {
        $relationName = Str::camel($relatedModel);
        return "public function $relationName()
    {
        return \$this->belongsTo(\\App\\Models\\$relatedModel::class);
    }";
    }

    /**
     * Generate hasMany relationship method.
     *
     * @param string $relatedModel
     * @return string
     */
    protected function generateHasManyRelationship($relatedModel)
    {
        $relationName = Str::camel(Str::plural($relatedModel));
        return "public function $relationName()
    {
        return \$this->hasMany(\\App\\Models\\$relatedModel::class);
    }";
    }

    /**
     * Generate belongsToMany relationship method.
     *
     * @param string $relatedModel
     * @return string
     */
    protected function generateBelongsToManyRelationship($relatedModel)
    {
        $relationName = Str::camel(Str::plural($relatedModel));
        return "public function $relationName()
    {
        return \$this->belongsToMany(\\App\\Models\\$relatedModel::class);
    }";
    }

    /**
     * Generate hasOne relationship method.
     *
     * @param string $relatedModel
     * @return string
     */
    protected function generateHasOneRelationship($relatedModel)
    {
        $relationName = Str::camel($relatedModel);
        return "public function $relationName()
    {
        return \$this->hasOne(\\App\\Models\\$relatedModel::class);
    }";
    }

    /**
     * Get the latest migration file for a given name.
     *
     * @param string $name
     * @return string
     */
    protected function getLatestMigration($name)
    {
        $migrations = File::glob(database_path('migrations/*_' . $name . '.php'));
        return basename(end($migrations));
    }

    /**
     * Load view template from stubs directory.
     *
     * @param string $name
     * @return string
     */


    protected function loadViewTemplate($name)
    {
        // First check if the template exists in the templates directory
        $templatePath = resource_path('views/templates/' . $name . '.blade.php');

        if (File::exists($templatePath)) {
            // Use the template from the templates directory
            return File::get($templatePath);
        }

        // If template doesn't exist in the templates directory, check in stubs directory
        $stubPath = app_path('Console/Commands/stubs/views/' . $name . '.stub');

        if (File::exists($stubPath)) {
            return File::get($stubPath);
        }

        // If no template is found, throw an exception
        throw new \Exception("Template file for '{$name}' not found. Please create the template in resources/views/templates/{$name}.blade.php");
    }





//    protected function loadViewTemplate($name)
//    {
//        // First check if the template exists in the custom templates directory
//        $templatePath = resource_path('views/templates/' . $name . '.blade.php');
//
//        if (File::exists($templatePath)) {
//            return File::get($templatePath);
//        }
//
//        // Otherwise use updated stubs based on the new template design
//        switch ($name) {
//            case 'index':
//                return '@extends(\'dashboard.layouts.master\')
//
//@section(\'content\')
//    <div class="app-content content container-fluid">
//        <div class="content-wrapper">
//            <div class="content-header row">
//                <div class="content-header-left col-md-6 col-xs-12 mb-1">
//                    <h2 class="content-header-title">{{modelNamePlural}} Management</h2>
//                </div>
//                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
//                    <div class="breadcrumb-wrapper col-xs-12">
//                        <ol class="breadcrumb">
//                            <li class="breadcrumb-item"><a href="{{ route(\'dashboard\') }}">Dashboard</a>
//                            </li>
//                            <li class="breadcrumb-item active">{{modelNamePlural}}
//                            </li>
//                        </ol>
//                    </div>
//                </div>
//            </div>
//            <div class="content-body">
//                <!-- Table head options start -->
//                <div class="row">
//                    <div class="col-xs-12">
//                        <div class="card">
//                            <div class="card-header">
//                                <h4 class="card-title">{{modelNamePlural}} List</h4>
//                                <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
//                                <div class="heading-elements">
//                                    <ul class="list-inline mb-0">
//                                        <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
//                                        <li><a data-action="reload"><i class="icon-reload"></i></a></li>
//                                        <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
//                                        <li><a data-action="close"><i class="icon-cross2"></i></a></li>
//                                    </ul>
//                                </div>
//                            </div>
//                            <div class="card-body collapse in">
//                                <div class="card-block card-dashboard">
//                                    <a href="{{ route(\'{{viewPath}}.create\') }}" class="btn btn-primary mb-1">
//                                        <i class="icon-plus2"></i> Add New {{modelName}}
//                                    </a>
//                                </div>
//                                <div class="table-responsive">
//                                    <table class="table">
//                                        <thead class="thead-inverse">
//                                        <tr>
//                                            <th>#</th>
//                                            {{tableHeaders}}
//                                            <th>Actions</th>
//                                        </tr>
//                                        </thead>
//                                        <tbody>
//                                        @forelse(${{modelNamePluralLowerCase}} as ${{modelNameSingularLowerCase}})
//                                            <tr>
//                                                <th scope="row">{{ $loop->iteration }}</th>
//                                                {{tableRows}}
//                                                <td>
//                                                    <a href="{{ route(\'{{viewPath}}.show\', ${{modelNameSingularLowerCase}}->id) }}" class="btn btn-info btn-sm">
//                                                        <i class="icon-eye6"></i> View
//                                                    </a>
//                                                    <a href="{{ route(\'{{viewPath}}.edit\', ${{modelNameSingularLowerCase}}->id) }}" class="btn btn-warning btn-sm">
//                                                        <i class="icon-pencil3"></i> Edit
//                                                    </a>
//                                                    <form action="{{ route(\'{{viewPath}}.destroy\', ${{modelNameSingularLowerCase}}->id) }}" method="POST" style="display: inline-block;">
//                                                        @csrf
//                                                        @method(\'DELETE\')
//                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this {{modelName}}?\');">
//                                                            <i class="icon-trash4"></i> Delete
//                                                        </button>
//                                                    </form>
//                                                </td>
//                                            </tr>
//                                        @empty
//                                            <tr>
//                                                <td colspan="{{ 2 + count(Schema::getColumnListing(\'{{viewPath}}\')) }}" class="text-center">No {{modelNamePlural}} found.</td>
//                                            </tr>
//                                        @endforelse
//                                        </tbody>
//                                    </table>
//                                </div>
//                            </div>
//                        </div>
//                    </div>
//                </div>
//                <!-- Table head options end -->
//            </div>
//        </div>
//    </div>
//@endsection';
//
//            case 'create':
//                return '@extends(\'dashboard.layouts.master\')
//
//@section(\'content\')
//    <div class="app-content content container-fluid">
//        <div class="content-wrapper">
//            <div class="content-header row">
//                <div class="content-header-left col-md-6 col-xs-12 mb-1">
//                    <h2 class="content-header-title">Create {{modelName}}</h2>
//                </div>
//                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
//                    <div class="breadcrumb-wrapper col-xs-12">
//                        <ol class="breadcrumb">
//                            <li class="breadcrumb-item"><a href="{{ route(\'dashboard\') }}">Dashboard</a>
//                            </li>
//                            <li class="breadcrumb-item"><a href="{{ route(\'{{viewPath}}.index\') }}">{{modelName}} Management</a>
//                            </li>
//                            <li class="breadcrumb-item active">Create New {{modelName}}
//                            </li>
//                        </ol>
//                    </div>
//                </div>
//            </div>
//            <div class="content-body">
//                <section id="basic-form-layouts">
//                    <div class="row match-height">
//                        <div class="col-md-12">
//                            <div class="card">
//                                <div class="card-header">
//                                    <h4 class="card-title" id="basic-layout-tooltip">Create New {{modelName}}</h4>
//                                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
//                                    <div class="heading-elements">
//                                        <ul class="list-inline mb-0">
//                                            <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
//                                            <li><a data-action="reload"><i class="icon-reload"></i></a></li>
//                                            <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
//                                            <li><a data-action="close"><i class="icon-cross2"></i></a></li>
//                                        </ul>
//                                    </div>
//                                </div>
//                                <div class="card-body collapse in">
//                                    <div class="card-block">
//                                        <div class="card-text">
//                                            <p>Please fill in all required fields to create a new {{modelName}}.</p>
//                                        </div>
//
//                                        <form class="form" method="POST" action="{{ route(\'{{viewPath}}.store\') }}" enctype="multipart/form-data">
//                                            @csrf
//                                            <div class="form-body">
//                                                {{formFields}}
//                                            </div>
//
//                                            <div class="form-actions">
//                                                <a href="{{ route(\'{{viewPath}}.index\') }}" class="btn btn-warning mr-1">
//                                                    <i class="icon-cross2"></i> Cancel
//                                                </a>
//                                                <button type="submit" class="btn btn-primary">
//                                                    <i class="icon-check2"></i> Save
//                                                </button>
//                                            </div>
//                                        </form>
//                                    </div>
//                                </div>
//                            </div>
//                        </div>
//                    </div>
//                </section>
//            </div>
//        </div>
//    </div>
//@endsection';
//
//            case 'edit':
//                return '@extends(\'dashboard.layouts.master\')
//
//@section(\'content\')
//    <div class="app-content content container-fluid">
//        <div class="content-wrapper">
//            <div class="content-header row">
//                <div class="content-header-left col-md-6 col-xs-12 mb-1">
//                    <h2 class="content-header-title">Edit {{modelName}}</h2>
//                </div>
//                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
//                    <div class="breadcrumb-wrapper col-xs-12">
//                        <ol class="breadcrumb">
//                            <li class="breadcrumb-item"><a href="{{ route(\'dashboard\') }}">Dashboard</a>
//                            </li>
//                            <li class="breadcrumb-item"><a href="{{ route(\'{{viewPath}}.index\') }}">{{modelName}} Management</a>
//                            </li>
//                            <li class="breadcrumb-item active">Edit {{modelName}}
//                            </li>
//                        </ol>
//                    </div>
//                </div>
//            </div>
//            <div class="content-body">
//                <section id="basic-form-layouts">
//                    <div class="row match-height">
//                        <div class="col-md-12">
//                            <div class="card">
//                                <div class="card-header">
//                                    <h4 class="card-title" id="basic-layout-tooltip">Edit {{modelName}} #{{ ${{modelNameLowerCase}}->id }}</h4>
//                                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
//                                    <div class="heading-elements">
//                                        <ul class="list-inline mb-0">
//                                            <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
//                                            <li><a data-action="reload"><i class="icon-reload"></i></a></li>
//                                            <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
//                                            <li><a data-action="close"><i class="icon-cross2"></i></a></li>
//                                        </ul>
//                                    </div>
//                                </div>
//                                <div class="card-body collapse in">
//                                    <div class="card-block">
//                                        <div class="card-text">
//                                            <p>Update the information for this {{modelName}}.</p>
//                                        </div>
//
//                                        <form class="form" method="POST" action="{{ route(\'{{viewPath}}.update\', ${{modelNameLowerCase}}->id) }}" enctype="multipart/form-data">
//                                            @csrf
//                                            @method(\'PUT\')
//                                            <div class="form-body">
//                                                {{formFields}}
//                                            </div>
//
//                                            <div class="form-actions">
//                                                <a href="{{ route(\'{{viewPath}}.index\') }}" class="btn btn-warning mr-1">
//                                                    <i class="icon-cross2"></i> Cancel
//                                                </a>
//                                                <button type="submit" class="btn btn-primary">
//                                                    <i class="icon-check2"></i> Update
//                                                </button>
//                                            </div>
//                                        </form>
//                                    </div>
//                                </div>
//                            </div>
//                        </div>
//                    </div>
//                </section>
//            </div>
//        </div>
//    </div>
//@endsection';
//
//            // Add other view templates as needed for show, etc.
//            default:
//                // Use original stub if the new template is not provided
//                return File::get(app_path('Console/Commands/stubs/views/' . $name . '.stub'));
//        }
//    }







    /**
     * Generate form field based on column type.
     *
     * @param string $column
     * @param string $type
     * @param string $modelName
     * @return string
     */
//    protected function generateFormField($column, $type, $modelName)
//    {
//        $label = Str::title(str_replace('_', ' ', $column));
//        $modelVariable = '$' . Str::camel($modelName);
//
//        // Foreign key field (select box)
//        if (Str::endsWith($column, '_id')) {
//            $relatedModel = Str::studly(Str::singular(str_replace('_id', '', $column)));
//            return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <select id="' . $column . '" name="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//                <option value="">Select ' . $relatedModel . '</option>
//                @foreach($' . Str::camel(Str::plural($relatedModel)) . ' as $' . Str::camel($relatedModel) . ')
//                    <option value="{{ $' . Str::camel($relatedModel) . '->id }}" {{ isset(' . $modelVariable . ') && ' . $modelVariable . '->' . $column . ' == $' . Str::camel($relatedModel) . '->id ? \'selected\' : \'\' }}>
//                        {{ $' . Str::camel($relatedModel) . '->name }}
//                    </option>
//                @endforeach
//            </select>
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//        }
//
//        // Other field types
//        switch ($type) {
//            case 'boolean':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <select id="' . $column . '" name="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//                <option value="0" {{ isset(' . $modelVariable . ') && !' . $modelVariable . '->' . $column . ' ? \'selected\' : \'\' }}>No</option>
//                <option value="1" {{ isset(' . $modelVariable . ') && ' . $modelVariable . '->' . $column . ' ? \'selected\' : \'\' }}>Yes</option>
//            </select>
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'text':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <textarea id="' . $column . '" rows="5" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                      name="' . $column . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}</textarea>
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'date':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <input type="date" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
//                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'datetime':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <input type="datetime-local" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
//                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'decimal':
//            case 'float':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <input type="number" step="0.01" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
//                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'integer':
//            case 'int':
//            case 'bigint':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <input type="number" step="1" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
//                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            case 'json':
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <textarea id="' . $column . '" rows="5" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                      name="' . $column . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">{{ isset(' . $modelVariable . ') ? json_encode(' . $modelVariable . '->' . $column . ', JSON_PRETTY_PRINT) : old(\'' . $column . '\') }}</textarea>
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//
//            default:
//                return '<div class="form-group">
//            <label for="' . $column . '">' . $label . '</label>
//            <input type="text" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
//                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
//                   placeholder="' . strtolower($label) . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="' . $label . '">
//            @error(\'' . $column . '\')
//                <div class="invalid-feedback">{{ $message }}</div>
//            @enderror
//        </div>';
//        }
//    }

    /**
     * Generate a detail field for the show view.
     *
     * @param string $column
     * @return string
     */
//    protected function generateDetailField($column)
//    {
//        $label = Str::title(str_replace('_', ' ', $column));
//
//        return '<div class="mb-3">
//        <strong>' . $label . ':</strong> {{ $' . Str::camel($this->argument('name')) . '->' . $column . ' }}
//    </div>';
//    }

    /**
     * Generate a table cell for the index view.
     *
     * @param string $column
     * @param string $type
     * @return string
     */
    protected function generateTableCell($column, $type)
    {
        $modelVariable = '$' . Str::camel($this->argument('name'));

        switch ($type) {
            case 'boolean':
                return '<td>{{ ' . $modelVariable . '->' . $column . ' ? \'Yes\' : \'No\' }}</td>';
            case 'date':
            case 'datetime':
                return '<td>{{ ' . $modelVariable . '->' . $column . ' ? ' . $modelVariable . '->' . $column . '->format(\'Y-m-d\') : \'\' }}</td>';
            default:
                return '<td>{{ ' . $modelVariable . '->' . $column . ' }}</td>';
        }
    }


    protected function addPermissions($modelName)
    {
        $this->info('Adding Permissions for: ' . $modelName);

        // Check if Spatie Permission package is installed
        if (!class_exists('\Spatie\Permission\Models\Permission')) {
            $this->warn('Spatie Permission package is not installed. Skipping permission creation.');
            return;
        }

        // Get the model name in lower case
        $modelNameLower = Str::lower($modelName);

        // Define permissions
        $permissions = [
            'view_' . $modelNameLower,
            'create_' . $modelNameLower,
            'edit_' . $modelNameLower,
            'delete_' . $modelNameLower,
        ];

        // Create each permission
        foreach ($permissions as $permission) {
            if (!\Spatie\Permission\Models\Permission::where('name', $permission)->exists()) {
                \Spatie\Permission\Models\Permission::create(['name' => $permission]);
                $this->info('Permission created: ' . $permission);
            } else {
                $this->info('Permission already exists: ' . $permission);
            }
        }

        // Attempt to assign these permissions to a 'super-admin' role if it exists
        try {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
            if ($superAdminRole) {
                $superAdminRole->givePermissionTo($permissions);
                $this->info('Permissions assigned to super-admin role.');
            }
        } catch (\Exception $e) {
            $this->warn('Could not assign permissions to roles: ' . $e->getMessage());
        }
    }

    protected function updateTranslationFile($modelName, $fields)
    {
        $this->info('Updating translation file for: ' . $modelName);

        $translationPath = lang_path('en/dashboard.php');

        // Create translation file if it doesn't exist
        if (!File::exists(dirname($translationPath))) {
            File::makeDirectory(dirname($translationPath), 0755, true);
        }

        if (!File::exists($translationPath)) {
            File::put($translationPath, "<?php\n\nreturn [\n\n];");
        }

        // Load existing translations
        $translations = include $translationPath;
        if (!is_array($translations)) {
            $translations = [];
        }

        $modelNameLower = Str::lower($modelName);
        $modelNamePlural = Str::lower(Str::plural($modelName));
        $modelNameTitle = Str::title(str_replace('_', ' ', $modelName));
        $modelNamePluralTitle = Str::title(str_replace('_', ' ', Str::plural($modelName)));

        // Add model-specific translations
        $modelTranslations = [
            $modelNameLower => [
                'title' => $modelNameTitle,
                'title_plural' => $modelNamePluralTitle,
                'management' => $modelNamePluralTitle . ' Management',
                'list' => $modelNamePluralTitle . ' List',
                'create' => 'Create ' . $modelNameTitle,
                'edit' => 'Edit ' . $modelNameTitle,
                'view' => 'View ' . $modelNameTitle,
                'delete' => 'Delete ' . $modelNameTitle,
                'add_new' => 'Add New ' . $modelNameTitle,
                'create_new' => 'Create New ' . $modelNameTitle,
                'update' => 'Update ' . $modelNameTitle,
                'show' => 'Show ' . $modelNameTitle,
                'actions' => 'Actions',
                'no_records' => 'No ' . $modelNamePluralTitle . ' found.',
                'fill_required' => 'Please fill in all required fields to create a new ' . $modelNameTitle . '.',
                'update_info' => 'Update the information for this ' . $modelNameTitle . '.',
                'delete_confirm' => 'Are you sure you want to delete this ' . $modelNameTitle . '?',

            ]
        ];

        // Add field translations
        foreach ($fields as $field) {
            $fieldName = trim(explode(':', $field)[0]);
            if (!in_array($fieldName, ['id', 'created_at', 'updated_at'])) {
                $fieldLabel = Str::title(str_replace('_', ' ', $fieldName));
                $modelTranslations[$modelNameLower]['fields'][$fieldName] = $fieldLabel;
            }
        }

        // Add common translations if they don't exist
        if (!isset($translations['common'])) {
            $translations['common'] = [
                'dashboard' => 'Dashboard',
                'save' => 'Save',
                'update' => 'Update',
                'cancel' => 'Cancel',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'view' => 'View',
                'actions' => 'Actions',
                'yes' => 'Yes',
                'no' => 'No',
                'select' => 'Select',
            ];
        }

        // Merge translations
        $translations = array_merge_recursive($translations, $modelTranslations);

        // Write updated translations to file
        $this->writeTranslationFile($translationPath, $translations);
    }

    protected function writeTranslationFile($path, $translations)
    {
        $content = "<?php\n\nreturn " . $this->arrayToString($translations, 1) . ";";
        File::put($path, $content);
    }

    protected function arrayToString($array, $indent = 0)
    {
        $indentStr = str_repeat('    ', $indent);
        $result = "[\n";

        foreach ($array as $key => $value) {
            $result .= $indentStr . "    '" . $key . "' => ";

            if (is_array($value)) {
                $result .= $this->arrayToString($value, $indent + 1);
            } else {
                $result .= "'" . addslashes($value) . "'";
            }

            $result .= ",\n";
        }

        $result .= $indentStr . "]";
        return $result;
    }

// Modified createViews function to include translation support
    protected function createViews($modelName, $tableName, $fields)
    {
        $this->info('Creating Views for: ' . $modelName);

        $viewPath = resource_path('views/dashboard/' . Str::kebab(Str::plural($modelName)));

        if (!File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
        }

        // Get column information from database if table exists
        $databaseColumns = [];
        try {
            if (Schema::hasTable($tableName)) {
                $databaseColumns = Schema::getColumnListing($tableName);
            }
        } catch (\Exception $e) {
            $this->warn('Could not fetch table schema: ' . $e->getMessage());
        }

        // Merge manually provided fields with database columns
        $columns = !empty($fields) ? $fields : $databaseColumns;

        // Define form fields based on column types
        $formFields = [];
        $tableHeaders = [];
        $tableRows = [];

        foreach ($columns as $column) {
            // Skip id, timestamps, and created_at/updated_at fields in forms
            if (in_array($column, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            // Parse field name and type
            if (strpos($column, ':') !== false) {
                list($column, $type) = explode(':', $column);
            } else {
                // Try to get type from DB if available
                $type = 'string'; // Default type
                if (!empty($databaseColumns)) {
                    try {
                        $columnType = DB::getSchemaBuilder()->getColumnType($tableName, $column);
                        switch ($columnType) {
                            case 'boolean':
                                $type = 'boolean';
                                break;
                            case 'integer':
                            case 'bigint':
                                $type = 'integer';
                                break;
                            case 'datetime':
                                $type = 'datetime';
                                break;
                            case 'date':
                                $type = 'date';
                                break;
                            case 'text':
                            case 'longtext':
                                $type = 'text';
                                break;
                            case 'decimal':
                            case 'float':
                                $type = 'decimal';
                                break;
                            default:
                                $type = 'string';
                        }
                    } catch (\Exception $e) {
                        $this->warn('Could not determine column type: ' . $e->getMessage());
                    }
                }
            }

            $column = trim($column);
            $type = trim($type);

            // Generate form field based on type
            $formFields[] = $this->generateFormField($column, $type, $modelName);

            // Generate table header with translation
            $tableHeaders[] = '<th>{{ __("dashboard.' . Str::lower($modelName) . '.fields.' . $column . '") }}</th>';

            // Generate table row cell
            $tableRows[] = $this->generateTableCell($column, $type);
        }

        // Create index view
        $indexTemplate = $this->loadViewTemplate('index');
        $indexTemplate = str_replace('{{modelName}}', $modelName, $indexTemplate);
        $indexTemplate = str_replace('{{modelNamePlural}}', Str::plural($modelName), $indexTemplate);
        $indexTemplate = str_replace('{{modelNamePluralLowerCase}}', Str::camel(Str::plural($modelName)), $indexTemplate);
        $indexTemplate = str_replace('{{modelNameSingularLowerCase}}', Str::camel($modelName), $indexTemplate);
        $indexTemplate = str_replace('{{modelNameLower}}', Str::lower($modelName), $indexTemplate);
        $indexTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $indexTemplate);
        $indexTemplate = str_replace('{{tableHeaders}}', implode("\n                ", $tableHeaders), $indexTemplate);
        $indexTemplate = str_replace('{{tableRows}}', implode("\n                ", $tableRows), $indexTemplate);

        File::put($viewPath . '/index.blade.php', $indexTemplate);

        // Create create view
        $createTemplate = $this->loadViewTemplate('create');
        $createTemplate = str_replace('{{modelName}}', $modelName, $createTemplate);
        $createTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $createTemplate);
        $createTemplate = str_replace('{{modelNameLower}}', Str::lower($modelName), $createTemplate);
        $createTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $createTemplate);
        $createTemplate = str_replace('{{formFields}}', implode("\n                ", $formFields), $createTemplate);

        File::put($viewPath . '/create.blade.php', $createTemplate);

        // Create edit view
        $editTemplate = $this->loadViewTemplate('edit');
        $editTemplate = str_replace('{{modelName}}', $modelName, $editTemplate);
        $editTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $editTemplate);
        $editTemplate = str_replace('{{modelNameLower}}', Str::lower($modelName), $editTemplate);
        $editTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $editTemplate);
        $editTemplate = str_replace('{{formFields}}', implode("\n                ", $formFields), $editTemplate);

        File::put($viewPath . '/edit.blade.php', $editTemplate);

        // Create show view
        $showTemplate = $this->loadViewTemplate('show');
        $showTemplate = str_replace('{{modelName}}', $modelName, $showTemplate);
        $showTemplate = str_replace('{{modelNameLowerCase}}', Str::camel($modelName), $showTemplate);
        $showTemplate = str_replace('{{modelNameLower}}', Str::lower($modelName), $showTemplate);
        $showTemplate = str_replace('{{viewPath}}', Str::kebab(Str::plural($modelName)), $showTemplate);

        // Generate detail fields
        $detailFields = [];
        foreach ($columns as $column) {
            if ($column === 'id') continue;

            if (strpos($column, ':') !== false) {
                list($column, $type) = explode(':', $column);
            }

            $column = trim($column);
            $detailFields[] = $this->generateDetailField($column, $modelName);
        }

        $showTemplate = str_replace('{{detailFields}}', implode("\n                ", $detailFields), $showTemplate);

        File::put($viewPath . '/show.blade.php', $showTemplate);

        // Update translation file
        $this->updateTranslationFile($modelName, $fields);
    }

// Modified generateFormField function with translations
    protected function generateFormField($column, $type, $modelName)
    {
        $modelVariable = '$' . Str::camel($modelName);
        $modelNameLower = Str::lower($modelName);

        // Foreign key field (select box)
        if (Str::endsWith($column, '_id')) {
            $relatedModel = Str::studly(Str::singular(str_replace('_id', '', $column)));
            return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <select id="' . $column . '" name="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
                <option value="">{{ __("dashboard.common.select") }} {{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</option>
                @foreach($' . Str::camel(Str::plural($relatedModel)) . ' as $' . Str::camel($relatedModel) . ')
                    <option value="{{ $' . Str::camel($relatedModel) . '->id }}" {{ isset(' . $modelVariable . ') && ' . $modelVariable . '->' . $column . ' == $' . Str::camel($relatedModel) . '->id ? \'selected\' : \'\' }}>
                        {{ $' . Str::camel($relatedModel) . '->name }}
                    </option>
                @endforeach
            </select>
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';
        }

        // Other field types with translations
        switch ($type) {
            case 'boolean':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <select id="' . $column . '" name="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
                <option value="0" {{ isset(' . $modelVariable . ') && !' . $modelVariable . '->' . $column . ' ? \'selected\' : \'\' }}>{{ __("dashboard.common.no") }}</option>
                <option value="1" {{ isset(' . $modelVariable . ') && ' . $modelVariable . '->' . $column . ' ? \'selected\' : \'\' }}>{{ __("dashboard.common.yes") }}</option>
            </select>
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'text':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <textarea id="' . $column . '" rows="5" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                      name="' . $column . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}</textarea>
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'date':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <input type="date" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'datetime':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <input type="datetime-local" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'decimal':
            case 'float':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <input type="number" step="0.01" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'integer':
            case 'int':
            case 'bigint':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <input type="number" step="1" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
                   data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            case 'json':
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <textarea id="' . $column . '" rows="5" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                      name="' . $column . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">{{ isset(' . $modelVariable . ') ? json_encode(' . $modelVariable . '->' . $column . ', JSON_PRETTY_PRINT) : old(\'' . $column . '\') }}</textarea>
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';

            default:
                return '<div class="form-group">
            <label for="' . $column . '">{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}</label>
            <input type="text" id="' . $column . '" class="form-control @error(\'' . $column . '\') is-invalid @enderror"
                   name="' . $column . '" value="{{ isset(' . $modelVariable . ') ? ' . $modelVariable . '->' . $column . ' : old(\'' . $column . '\') }}"
                   placeholder="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}">
            @error(\'' . $column . '\')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>';
        }
    }

// Modified generateDetailField function with translations
    protected function generateDetailField($column, $modelName)
    {
        $modelNameLower = Str::lower($modelName);

        return '<div class="mb-3">
        <strong>{{ __("dashboard.' . $modelNameLower . '.fields.' . $column . '") }}:</strong> {{ $' . Str::camel($modelName) . '->' . $column . ' }}
    </div>';
    }

// Modified handle function - add translation update call

}
