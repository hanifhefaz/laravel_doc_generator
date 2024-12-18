<?php

namespace HanifHefaz\DocumentationGenerator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class GenerateDocumentationCommand extends Command
{
    protected $signature = 'docs:generate {path} {--excludeDir= : Comma-separated list of directories to exclude}';
    protected $description = 'Generate technical documentation for the Laravel project.';

    public function handle()
    {
        $path = $this->argument('path');
        $projectName = $this->ask('Please enter the project name');
        $date = $this->ask('Please enter the date (e.g., 2024-12-16)');
        $languages = $this->ask('Enter the languages used (e.g., PHP, JavaScript)');
        $frameworksInput = $this->ask('Enter the frameworks used (e.g., Laravel, Vue.js)');
        $databases = $this->ask('Enter the databases used (e.g., MySQL, PostgreSQL)');
        $technologies = $this->ask('Enter the front-end technologies used (e.g., HTML, CSS, Bootstrap)');
        $devToolsInput = $this->ask('Enter development tools (comma-separated, e.g., phpunit, faker)');

        $format = $this->choice('Select the documentation format', ['md', 'html'], 0);

        $documentation = '';

        // Generate documentation sections
        $documentation .= $this->getHeaderDocumentation($projectName, $date);
        $documentation .= $this->getDatabaseDocumentation($path);
        $documentation .= $this->getProjectStructureDocumentation($path);
        $documentation .= $this->getRoutesDocumentation();
        $documentation .= $this->getModelsDocumentation($path);
        $documentation .= $this->getControllersDocumentation($path);
        $documentation .= $this->getViewsStructure($path);
        $documentation .= $this->getMigrationsDocumentation($path);
        $documentation .= $this->getSeedersDocumentation($path);
        $documentation .= $this->getEnvironmentDocumentation();
        $documentation .= $this->getTestsDocumentation($path);
        $documentation .= $this->getMiddlewareDocumentation();
        $documentation .= $this->getProjectRequirements($languages, $frameworksInput, $databases, $technologies, $devToolsInput);

        // Save the documentation to a file
        $this->saveDocumentation($path, $documentation, $format, $projectName);
        $this->info('Documentation generated successfully at ' . $path . '/documentation.md');
    }

    protected function saveDocumentation($path, $documentation, $format, $projectName)
    {
        switch ($format) {
            case 'md':
                file_put_contents($path . "/{$projectName}_documentation.md", $documentation);
                break;
            case 'html':
                file_put_contents($path . "/{$projectName}_documentation.html", $this->formatHtmlContent($documentation));
                break;
        }
    }

    protected function formatHtmlContent($content)
    {
        // Convert Markdown to HTML if necessary
        $content = $this->convertMarkdownToHtml($content);

        // Add basic HTML structure and styles
        return "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                margin: 20px;
            }
            h1 {
                text-align: center;
                font-size: 2em;
            }
            h3 {
                text-align: center;
            }
            h2 {
                border-bottom: 2px solid #000;
            }
            pre {
                background-color: #f4f4f4;
                padding: 10px;
                border-radius: 5px;
            }
            ul {
                list-style-type: disc;
                margin-left: 20px;
            }
            code {
                background-color: #e8e8e8;
                padding: 2px 4px;
                border-radius: 3px;
            }
        </style>
    </head>
    <body>
        $content
    </body>
    </html>
    ";
    }

    protected function convertMarkdownToHtml($markdown)
    {
        // Use a Markdown parser library, like Parsedown, to convert Markdown to HTML
        $parser = new \Parsedown();
        return $parser->text($markdown);
    }

    protected function getHeaderDocumentation($projectName, $date)
    {
        $doc = "<h1 style=\"text-align: center; font-size: 2em;\">$projectName</h1>\n"; // Title centered and larger
        $doc .= "<h3 style=\"text-align: center;\">Date: $date</h3>\n\n"; // Date centered

        // Generate Table of Contents
        $doc .= "## Table of Contents\n";
        $doc .= "- [Project Structure](#project-structure)\n";
        $doc .= "- [Routes Documentation](#routes-documentation)\n";
        $doc .= "- [Models Documentation](#models-documentation)\n";
        $doc .= "- [Controllers Documentation](#controllers-documentation)\n";
        $doc .= "- [Views Structure](#views-structure)\n";
        $doc .= "- [Migrations Documentation](#migrations-documentation)\n";
        $doc .= "- [Seeders Documentation](#seeders-documentation)\n";
        $doc .= "- [Environment Documentation](#environment-documentation)\n";
        $doc .= "- [Tests Documentation](#tests-documentation)\n";
        $doc .= "- [Middleware Documentation](#middleware-documentation)\n";
        $doc .= "- [Project Requirements](#project-requirements)\n\n";

        return $doc;
    }

    protected function getProjectStructureDocumentation($path)
    {
        $excludedDirs = $this->option('excludeDir') ? explode(',', $this->option('excludeDir')) : [];
        $doc = "```\n"; // Start a code block
        $doc .= $this->scanRootDirectory($path, '', true, $excludedDirs); // Pass excluded directories
        $doc .= "```\n"; // End the code block

        return $doc;
    }

    protected function scanRootDirectory($directory, $prefix, $isLast, $excludedDirs)
    {
        $doc = '';
        $files = array_diff(scandir($directory), ['..', '.']);

        // Filter out excluded directories
        $files = array_filter($files, function ($file) use ($directory, $excludedDirs) {
            return !is_dir($directory . '/' . $file) || !in_array($file, $excludedDirs);
        });

        $totalFiles = count($files);
        foreach ($files as $index => $file) {
            $fullPath = $directory . '/' . $file;
            $isDir = is_dir($fullPath);

            // Determine if this is the last item in the current directory
            $isLastItem = $index === $totalFiles - 1;

            // Add the prefix for tree structure
            $doc .= $prefix . ($isLastItem ? '└── ' : '├── ') . $file . "\n";

            // If it's a directory, recursively scan it
            if ($isDir) {
                // Create a new prefix for subdirectories
                $newPrefix = $prefix . ($isLastItem ? '    ' : '│   ');
                $doc .= $this->scanRootDirectory($fullPath, $newPrefix, false, $excludedDirs); // Pass excluded directories
            }
        }

        return $doc;
    }

    protected function getRoutesDocumentation()
    {
        $routes = Route::getRoutes();
        $doc = "## Routes Documentation\n\n";

        $groupedRoutes = [];

        // Group routes by controller
        foreach ($routes as $route) {
            $actionName = $route->getActionName();
            $controller = strstr($actionName, '@', true); // Get the controller name
            $groupedRoutes[$controller][] = $route;
        }

        // Generate documentation with one example per controller
        foreach ($groupedRoutes as $controller => $routes) {
            $doc .= "### Controller: `$controller`\n\n";

            // Include details for each route
            foreach ($routes as $index => $route) {
                $number = $index + 1; // Start numbering from 1
                $doc .= "- **{$number}. URI**: `{$route->uri}`\n";
                $doc .= '  - **Method**: ' . implode(', ', $route->methods()) . "\n";
                $doc .= "  - **Action**: `{$route->getActionName()}`\n";
                $doc .= '  - **Middleware**: ' . implode(', ', $route->gatherMiddleware()) . "\n";
                $doc .= '  - **Parameters**: ' . json_encode($route->parameterNames()) . "\n\n";

                // Add example for the first route only
                if ($index === 0) {
                    $doc .= $this->getRouteExample($controller, $route);
                }
            }

            // Add a horizontal line after each controller's routes
            $doc .= "---\n\n";
        }

        return $doc;
    }

    protected function getRouteExample($controller, $route)
    {
        // Extract the action name and URI
        $actionName = $route->getActionMethod(); // Just the method name
        $uri = $route->uri;
        $methods = implode(', ', $route->methods());

        // Remove the namespace from the controller class
        $controllerClass = class_basename($controller);

        // Construct the route definition code
        $exampleCode = "```php\n";
        $exampleCode .= "// Example route definition for $controllerClass\n";
        $exampleCode .= "Route::{$methods}('$uri', [$controllerClass::class, '$actionName'])->name('$actionName');\n";
        $exampleCode .= "```";

        return "  - **Example Route Definition**:\n" . $exampleCode . "\n";
    }

    protected function getModelsDocumentation($path)
    {
        $modelsPath = $path . '/app/Models';
        $models = array_diff(scandir($modelsPath), ['..', '.']);
        $doc = "## Models Documentation\n\n";

        foreach ($models as $model) {
            if (pathinfo($model, PATHINFO_EXTENSION) === 'php') {
                $modelName = pathinfo($model, PATHINFO_FILENAME);
                $doc .= "- **Model**: `$modelName`\n";

                // Reflection for additional details
                $reflection = new \ReflectionClass("App\\Models\\$modelName");

                // List fillable fields
                if ($reflection->hasProperty('fillable')) {
                    $fillableProperty = $reflection->getProperty('fillable');
                    $fillableProperty->setAccessible(true); // Allow access to protected property
                    $fillableFields = $fillableProperty->getValue(new ("App\\Models\\$modelName"));

                    $doc .= "  - **Fillable Fields**:\n";
                    foreach ($fillableFields as $field) {
                        $doc .= "    - `$field`\n";
                    }
                }

                // List methods defined in the model
                $doc .= "  - **Methods**:\n";
                $definedMethods = [];
                foreach ($reflection->getMethods() as $method) {
                    // Check if the method is defined in this class (not inherited)
                    if ($method->class === $reflection->getName()) {
                        // Exclude specific inherited methods from traits
                        $excludedMethods = ['forceDelete', 'performDeleteOnModel', 'bootSoftDeletes', 'initializeSoftDeletes', 'forceDeleteQuietly', 'runSoftDelete', 'restore', 'restoreQuietly', 'trashed', 'softDeleted', 'restoring', 'restored', 'forceDeleting', 'forceDeleted', 'isForceDeleting', 'getDeletedAtColumn', 'getQualifiedDeletedAtColumn', 'factory', 'newFactory'];

                        if (!in_array($method->getName(), $excludedMethods)) {
                            $definedMethods[] = $method->getName();
                        }
                    }
                }

                // Add defined methods to documentation
                foreach ($definedMethods as $methodName) {
                    $doc .= "    - `{$methodName}()`\n";
                }

                $doc .= "\n";
            }
        }

        return $doc;
    }

    protected function getControllersDocumentation($path)
    {
        $controllersPath = $path . '/app/Http/Controllers';
        $controllers = array_diff(scandir($controllersPath), ['..', '.']);
        $doc = "## Controllers Documentation\n\n";

        $controllerCount = 1; // Initialize a counter for controllers

        foreach ($controllers as $controller) {
            if (pathinfo($controller, PATHINFO_EXTENSION) === 'php') {
                $controllerName = pathinfo($controller, PATHINFO_FILENAME);
                $doc .= "- **{$controllerCount}. Controller**: `$controllerName`\n";

                // Reflection for additional methods
                $reflection = new \ReflectionClass("App\\Http\\Controllers\\$controllerName");
                $methods = [];

                foreach ($reflection->getMethods() as $method) {
                    if ($method->isPublic() && $method->getDeclaringClass()->getName() === $reflection->getName()) {
                        // Get the method name
                        $methodName = $method->getName();

                        // Get the doc comment
                        $docComment = $method->getDocComment();
                        $comment = '';

                        // Extract comment, if it exists
                        if ($docComment) {
                            // Remove the comment's asterisks and trim whitespace
                            $comment = trim(preg_replace('/\s*\*\s*/', ' ', $docComment));
                            $comment = preg_replace('/^\/\*\*|\*\/$/', '', $comment);
                        }

                        // Append method information to documentation
                        $doc .= "  - **Method**: `{$methodName}`\n";
                        if ($comment) {
                            $doc .= "    - **Description**: $comment\n";
                        }

                        // Collect methods for random selection
                        $methods[] = $method;
                    }
                }

                // Add a random method example if any methods exist
                if (!empty($methods)) {
                    $randomMethod = $methods[array_rand($methods)]; // Select a random method
                    $methodCode = $this->getMethodCode($randomMethod); // Get the method code
                    $doc .= "  - **Random Method Example**:\n";
                    $doc .= "```php\n$methodCode\n```\n";
                }

                $doc .= "\n---\n\n"; // Add a horizontal line after each controller
                $controllerCount++; // Increment the controller counter
            }
        }

        return $doc;
    }

    // Helper method to get the code of a method
    protected function getMethodCode(\ReflectionMethod $method)
    {
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $lines = file($method->getFileName());
        $methodCode = '';

        // Extract the method lines
        for ($i = $startLine - 1; $i < $endLine; $i++) {
            $methodCode .= rtrim($lines[$i]) . PHP_EOL;
        }

        return $methodCode;
    }

    protected function getViewsStructure($path)
    {
        $viewsPath = $path . '/resources/views';
        $doc = "## Views Structure Documentation\n\n";

        if (!is_dir($viewsPath)) {
            $doc .= "No views directory found at `resources/views`.\n";
            return $doc;
        }

        // Call the recursive function to scan views
        $doc .= $this->scanViews($viewsPath, 'resources/views/');

        return $doc;
    }

    protected function scanViews($directory, $prefix)
    {
        $doc = '';
        $files = array_diff(scandir($directory), ['..', '.']);

        foreach ($files as $file) {
            $fullPath = $directory . '/' . $file;
            if (is_dir($fullPath)) {
                // Document the directory
                $doc .= "{$prefix}|_____ {$file}\n";
                // Call the function recursively for the subdirectory
                $doc .= $this->scanViews($fullPath, $prefix . '    ' . $file . '/'); // Indent for subdirectory
            } else {
                // Document all files
                $doc .= "{$prefix}|_____ {$file}\n";
                $doc .= $this->analyzeBladeView($fullPath);
            }
        }

        return $doc;
    }

    protected function analyzeBladeView($filePath)
    {
        $content = file_get_contents($filePath);
        $insights = "## View: `{$filePath}`\n\n";

        // Check for the layout being extended
        if (preg_match('/@extends\([\'"](.+?)[\'"]\)/', $content, $matches)) {
            $insights .= "- **Layout**: Extends `{$matches[1]}`\n";
        }

        // Check for sections
        preg_match_all('/@section\([\'"](.+?)[\'"]\)/', $content, $sectionMatches);
        if (!empty($sectionMatches[1])) {
            $insights .= '- **Sections**: ' . implode(', ', $sectionMatches[1]) . "\n";
        }

        // Check for includes
        preg_match_all('/@include\([\'"](.+?)[\'"]\)/', $content, $includeMatches);
        if (!empty($includeMatches[1])) {
            $insights .= '- **Includes**: ' . implode(', ', $includeMatches[1]) . "\n";
        }

        // Check for components
        preg_match_all('/<x-([a-zA-Z0-9\-]+)/', $content, $componentMatches);
        if (!empty($componentMatches[0])) {
            $insights .= '- **Components**: ' . implode(', ', $componentMatches[0]) . "\n";
        }

        // Check for conditional statements
        if (strpos($content, '@if') !== false) {
            $insights .= "- **Conditional Rendering**: Contains conditional statements.\n";
        }

        // Check for loops
        if (strpos($content, '@foreach') !== false) {
            $insights .= "- **Loops**: Contains loops for iterating over collections.\n";
        }

        // Check for CSRF token
        if (strpos($content, '@csrf') !== false) {
            $insights .= "- **CSRF Protection**: CSRF token included.\n";
        }

        // Check for pagination
        if (strpos($content, 'links') !== false) {
            $insights .= "- **Pagination**: Includes pagination for navigating lists.\n";
        }

        // Check for form methods
        if (strpos($content, '<form') !== false) {
            $insights .= "- **Form Handling**: Contains a form for user input.\n";
        }

        // Check for Blade directives
        $bladeDirectives = ['@yield', '@parent', '@push', '@stack', '@forelse', '@empty', '@switch', '@case', '@break', '@default', '@continue', ' @endforelse'];

        foreach ($bladeDirectives as $directive) {
            if (strpos($content, $directive) !== false) {
                $insights .= "- **Directive Found**: {$directive}\n";
            }
        }

        // Add a check for custom directives
        preg_match_all('/@.+/', $content, $customDirectives);
        if (!empty($customDirectives[0])) {
            $insights .= '- **Custom Directives**: ' . implode(', ', $customDirectives[0]) . "\n";
        }

        return $insights;
    }

    protected function getMigrationsDocumentation($path)
    {
        $migrationsPath = $path . '/database/migrations';
        $migrations = array_diff(scandir($migrationsPath), ['..', '.']);
        $doc = "## Migrations Documentation\n\n";

        foreach ($migrations as $migration) {
            if (pathinfo($migration, PATHINFO_EXTENSION) === 'php') {
                $migrationName = pathinfo($migration, PATHINFO_FILENAME);
                $doc .= "- **Migration**: `$migrationName`\n";

                // Get the full path to the migration file
                $migrationFile = $migrationsPath . '/' . $migration;

                // Extract fields from the migration file
                $fields = $this->extractFieldsFromMigration($migrationFile);
                if (!empty($fields)) {
                    $doc .= "  - **Fields**:\n";
                    foreach ($fields as $field) {
                        $doc .= "    - `$field[name]` (Type: `$field[type]`";
                        if (isset($field['nullable'])) {
                            $doc .= ", Nullable: `{$field['nullable']}`";
                        }
                        if (isset($field['default'])) {
                            $doc .= ", Default: `{$field['default']}`";
                        }
                        $doc .= ")\n";
                    }
                } else {
                    $doc .= "  - **Fields**: No fields found.\n";
                }
            }
        }

        return $doc;
    }

    protected function extractFieldsFromMigration($migrationFile)
    {
        $fields = [];
        // Read the file's content
        $content = file_get_contents($migrationFile);

        // Use regex to match column definitions
        // This regex captures the data type and additional properties
        $pattern = '/\$table->(\w+)\(\s*[\'"](\w+)[\'"]\s*(,?\s*(\d+)?\s*)?(,?\s*(.*?))?\);/';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fieldData = [
                'name' => $match[2],
                'type' => $match[1],
            ];

            // Check for additional properties
            if (isset($match[6])) {
                $properties = explode(',', $match[6]);
                foreach ($properties as $property) {
                    $property = trim($property);
                    if (strpos($property, 'nullable') !== false) {
                        $fieldData['nullable'] = true;
                    }
                    if (preg_match('/default\(\s*(.+?)\s*\)/', $property, $defaultMatch)) {
                        $fieldData['default'] = trim($defaultMatch[1]);
                    }
                }
            }

            $fields[] = $fieldData;
        }

        return $fields;
    }

    protected function getEnvironmentDocumentation()
    {
        $doc = "## Environment Configuration\n\n";
        $envPath = base_path('.env');

        if (file_exists($envPath)) {
            $envContent = file($envPath);
            foreach ($envContent as $line) {
                if (!empty($line) && !str_starts_with($line, '#')) {
                    $doc .= '- **' . trim($line) . "**\n";
                }
            }
        } else {
            $doc .= "No .env file found.\n";
        }

        return $doc;
    }

    protected function getTestsDocumentation($path)
    {
        $testsPath = $path . '/tests';
        $testFiles = array_diff(scandir($testsPath), ['..', '.']);
        $doc = "## Tests Documentation\n\n";

        foreach ($testFiles as $testFile) {
            if (pathinfo($testFile, PATHINFO_EXTENSION) === 'php') {
                $testName = pathinfo($testFile, PATHINFO_FILENAME);
                $doc .= "- **Test**: `$testName`\n";
            }
        }

        return $doc;
    }

    protected function getMiddlewareDocumentation()
    {
        $middleware = app()->router->getMiddleware(); // Get middleware from the router
        $doc = "## Middleware Documentation\n\n";

        foreach ($middleware as $key => $value) {
            $doc .= "- **Middleware**: `{$key}` - `{$value}`\n";

            // Use reflection to get insights about the middleware
            $reflection = new \ReflectionClass($value);
            $doc .= "  - **Description**: `{$reflection->getDocComment()}`\n"; // Description from the doc comment
        }

        return $doc;
    }

    protected function getSeedersDocumentation($path)
    {
        $seedersPath = $path . '/database/seeders';
        $seeders = array_diff(scandir($seedersPath), ['..', '.']);
        $doc = "## Seeders Documentation\n\n";

        foreach ($seeders as $seeder) {
            if (pathinfo($seeder, PATHINFO_EXTENSION) === 'php') {
                $seederName = pathinfo($seeder, PATHINFO_FILENAME);
                $doc .= "- **Seeder**: `$seederName`\n";

                // Use reflection to get insights about the seeder
                $reflection = new \ReflectionClass("Database\\Seeders\\$seederName");
                $doc .= "  - **Description**: `{$reflection->getDocComment()}`\n"; // Description from the doc comment

                // Optionally, you can analyze the run() method
                if ($reflection->hasMethod('run')) {
                    $method = $reflection->getMethod('run');
                    $doc .= "  - **Run Method Description**: `{$method->getDocComment()}`\n"; // Description of the run method
                }
            }
        }

        return $doc;
    }
    protected function getDatabaseDocumentation()
{
    $doc = "## Database Documentation\n\n";
    $plantUmlCode = "@startuml\n\n";

    // Get all table names
    $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
    $tableFields = [];
    $relationships = [];

    // Collect field names and foreign keys
    foreach ($tables as $table) {
        $columns = Schema::getColumnListing($table);
        $tableFields[$table] = $columns;

        // Fetch foreign keys for relationships
        $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table);
        foreach ($foreignKeys as $foreignKey) {
            $localColumn = $foreignKey->getLocalColumns()[0];
            $foreignTable = $foreignKey->getForeignTableName();
            $relationships[] = [
                'local' => $table,
                'foreign' => $foreignTable,
                'localColumn' => $localColumn,
                'foreignColumn' => $foreignKey->getForeignColumns()[0] // Assuming one foreign column
            ];
        }
    }

    // Generate PlantUML code for each table
    foreach ($tableFields as $table => $columns) {
        $plantUmlCode .= "class $table {\n";
        foreach ($columns as $column) {
            $plantUmlCode .= "    + $column\n"; // Mark columns as public
        }
        $plantUmlCode .= "}\n\n";
    }

    // Add relationships to PlantUML
    foreach ($relationships as $relation) {
        $plantUmlCode .= "{$relation['local']} --> {$relation['foreign']} : `{$relation['localColumn']}` references `{$relation['foreignColumn']}`\n";
    }

    $plantUmlCode .= "@enduml\n";

    // Save the PlantUML code to a file and generate the image
    $pumlFile = 'database_diagram.puml';
    file_put_contents($pumlFile, $plantUmlCode);

    // Generate the diagram image (e.g., using PlantUML CLI or similar)
    $imageFile = 'database_diagram.png'; // Specify the image file name
    exec("java -jar C:\Users\Hifaz\Downloads\plantuml-1.2024.8.jar $pumlFile"); // Adjust the path to your PlantUML jar

    // Link to the generated image in the documentation
    $doc .= "![Database Diagram](/$imageFile)\n\n"; // Adjust the path based on your server setup

    return $doc;
}
    protected function getProjectRequirements($languages, $frameworksInput, $databases, $technologies, $devToolsInput)
    {
        $doc = "## Project Requirements\n\n";

        // Process languages
        $doc .= "- **Languages**: $languages\n";

        // Process frameworks
        $frameworks = explode(',', $frameworksInput);
        $frameworks = array_map('trim', $frameworks); // Trim each framework name
        $doc .= '- **Frameworks**: ' . implode(', ', $frameworks) . "\n";

        // Process databases
        $doc .= "- **Databases**: $databases\n";

        // Process front-end technologies
        $doc .= "- **Technologies**: $technologies\n";

        // Process development tools
        $doc .= "- **Development Tools**:\n";
        $devTools = explode(',', $devToolsInput);
        $devTools = array_map('trim', $devTools); // Trim each tool name

        foreach ($devTools as $tool) {
            $doc .= "  - `$tool`\n";
        }

        return $doc;
    }

}
