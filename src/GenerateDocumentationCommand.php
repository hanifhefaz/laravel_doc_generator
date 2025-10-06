<?php

namespace HanifHefaz\DocumentationGenerator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        $documentation .= $this->getDocumentationOverview();
        $documentation .= $this->getProjectRequirementsDocumentation();
        $documentation .= $this->getProjectStructureDocumentation($path);
        $documentation .= $this->getRoutesDocumentation();
        $documentation .= $this->getSimpleModelDiagram($path);
        $documentation .= $this->getModelsSchemaDiagram($path);
        $documentation .= $this->getDatabaseSchemaAdvanced($path);
        $documentation .= $this->getModelsDocumentation($path);
        $documentation .= $this->getControllersDocumentation($path);
        $documentation .= $this->getViewsStructure($path);
        $documentation .= $this->getMigrationsDocumentation($path);
        $documentation .= $this->getSeedersDocumentation($path);
        $documentation .= $this->getEnvironmentDocumentation();
        $documentation .= $this->getTestsDocumentation($path);
        $documentation .= $this->getMiddlewareDocumentation();
        $documentation .= $this->getDocumentationSummary();
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
        $doc .= "- [Documentation Overview](#documentation-overview)\n";
        $doc .= "- [Project Requirements and Installation](#project-requirements-and-installation)\n";
        $doc .= "- [Project Structure](#project-structure)\n";
        $doc .= "- [Routes Documentation](#routes-documentation)\n";
        $doc .= "- [Simple Eloquent Class Diagram](#simple-eloquent-class-diagram)\n";
        $doc .= "- [Database Schema Diagram](#database-schema-diagram)\n";
        $doc .= "- [Database Schema Advanced](#database-schema-advanced)\n";
        $doc .= "- [Models Documentation](#models-documentation)\n";
        $doc .= "- [Controllers Documentation](#controllers-documentation)\n";
        $doc .= "- [Views Documentation](#views-documentation)\n";
        $doc .= "- [Migrations Documentation](#migrations-documentation)\n";
        $doc .= "- [Seeders Documentation](#seeders-documentation)\n";
        $doc .= "- [Environment Configuration](#environment-configuration)\n";
        $doc .= "- [Tests Documentation](#tests-documentation)\n";
        $doc .= "- [Middleware Documentation](#middleware-documentation)\n";

        return $doc;
    }

    protected function getDocumentationOverview()
    {
        $markdown = <<<MD
        ## Documentation Overview

        This documentation provides a complete overview of the Laravel application, including every major part of the system. 

        MD;

        return $markdown;
    }

    protected function getProjectRequirementsDocumentation()
    {
        $markdown= <<<MD
        ## Project Requirements and Installation

        This section outlines all technical and software dependencies required to install, configure, and operate the Laravel application successfully. It also provides a complete guide for setting up the project from scratch from cloning the repository to running it locally for the first time.

        The goal of this documentation is to ensure a smooth and consistent setup experience for every developer, tester, or administrator working on the project. It defines all environment prerequisites, installation steps, and configuration details necessary to prepare a fully functional Laravel development environment.

        ---

        ### 1. Core Software Requirements

        | Component | Required Version | Description |
        |------------|------------------|--------------|
        | **PHP** | 8.1 or higher | Required to run the Laravel framework and its dependencies. |
        | **Laravel Framework** | 10.x | Provides the MVC architecture and built-in features for routing, ORM, and templating. |
        | **Composer** | 2.6 or higher | Dependency manager used for installing and managing Laravel packages. |
        | **Node.js & NPM** | Node.js 18.x / NPM 9.x | Used to compile and manage frontend assets via Vite. |
        | **Database Engine** | MySQL 8.0 / PostgreSQL 15 | Primary database system for storing and managing application data. |
        | **Web Server** | Apache 2.4 / Nginx 1.22 | Required to serve the application and handle HTTP requests. |

        ---

        ### 2. PHP Extensions

        Ensure the following PHP extensions are enabled in your `php.ini` configuration:

        ```
        OpenSSL
        PDO
        Mbstring
        Tokenizer
        XML
        Ctype
        Fileinfo
        BCMath
        JSON
        GD
        ```

        ---

        ### 3. Optional and Recommended Dependencies

        | Dependency | Purpose |
        |-------------|----------|
        | **Redis** | Used for caching, session management, and queue handling. |
        | **Memcached** | Optional caching driver for enhanced performance. |
        | **Supervisor** | Manages and monitors Laravel queue workers. |
        | **MailHog / Mailtrap** | Recommended for local email testing. |
        | **Laravel Telescope** | Developer tool for debugging and performance monitoring. |
        | **Laravel Horizon** | Dashboard for managing and visualizing Redis queues. |

        ---

        ### 4. Development Tools and IDE Recommendations

        | Tool | Recommended Version | Purpose |
        |------|---------------------|----------|
        | **Visual Studio Code** | Latest | Recommended IDE with Laravel-specific extensions (e.g., Blade Snippets, PHP Intelephense). |
        | **PHPStorm** | Latest | Alternative IDE for advanced Laravel development. |
        | **Git** | 2.40 or higher | Version control system for collaboration and source management. |
        | **Postman** | Latest | Used for API testing and request validation. |
        | **Docker / Laravel Sail** | Latest | Optional containerized environment for consistent local setup. |

        ---

        ### 5. System Configuration

        - **Memory Limit:** Minimum 512 MB (recommended 1 GB or higher)  
        - **Disk Space:** At least 500 MB free space  
        - **OS Compatibility:** Linux (Ubuntu 22.04+), macOS Monterey+, or Windows 10/11  
        - **Timezone:** Ensure the system timezone matches the application’s configuration (e.g., `UTC`)  

        ---

        ### 6. Cloning the Project

        To set up the project locally, begin by cloning the repository from your version control platform (e.g., GitLab or GitHub):  

        ```bash
        # Clone the repository
        git clone https://github.com/your-organization/your-laravel-project.git

        # Navigate into the project directory
        cd your-laravel-project
        ```

        ---

        ### 7. Installing Dependencies

        Once the project has been cloned, install all required dependencies using Composer and NPM:

        ```bash
        # Install PHP dependencies
        composer install

        # Install JavaScript dependencies
        npm install
        ```

        ---

        ### 8. Environment Configuration

        Create your environment file based on the provided example and configure your local settings:

        ```bash
        # Copy example environment file
        cp .env.example .env
        ```

        Then, open the `.env` file and update the following variables according to your local setup:

        ```env
        APP_NAME="MyLaravelApp"
        APP_ENV=local
        APP_DEBUG=true
        APP_URL=http://localhost:8000

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=my_laravel_db
        DB_USERNAME=root
        DB_PASSWORD=secret

        CACHE_DRIVER=file
        QUEUE_CONNECTION=sync
        SESSION_DRIVER=file
        ```

        ---

        ### 9. Generating the Application Key

        Laravel requires a unique key for encryption. Generate it using the Artisan command:

        ```bash
        php artisan key:generate
        ```

        This automatically sets the `APP_KEY` value in your `.env` file.

        ---

        ### 10. Database Setup

        Run database migrations and seeders to build and populate the database:

        ```bash
        php artisan migrate --seed
        ```

        This command creates all necessary tables and inserts initial or sample data (if seeders are defined).

        ---

        ### 11. Building Front-End Assets

        If the project includes front-end components (such as Blade templates, Vue, or React), compile them using Vite:

        ```bash
        npm run dev
        ```

        To build assets for production, use:

        ```bash
        npm run build
        ```

        ---

        ### 12. Running the Application

        Finally, start the Laravel development server:

        ```bash
        php artisan serve
        ```

        Once the server is running, open your browser and visit:  
        👉 **[http://localhost:8000](http://localhost:8000)**

        You should now see the Laravel welcome page or your application’s homepage, confirming a successful installation.

        ---

        ### 13. Common Troubleshooting

        | Issue | Possible Solution |
        |--------|--------------------|
        | **500 Internal Server Error** | Check file permissions for `storage/` and `bootstrap/cache/`. |
        | **Database connection error** | Verify your `.env` database credentials and restart the MySQL server. |
        | **Node build issues** | Delete `node_modules` and run `npm install` again. |
        | **Missing .env file** | Recreate it from `.env.example` and regenerate the app key. |

        ---
        MD;

        return $markdown;
    }

    protected function getProjectStructureDocumentation($path)
    {
        $excludedDirs = $this->option('excludeDir') ? explode(',', $this->option('excludeDir')) : [];
        $doc = "## Project's Structure:\n";
        $doc .= "```\n"; // Start a code block
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
        $controllerCount = 1; // Initialize a counter for controllers

        // Group routes by controller
        foreach ($routes as $route) {
            $actionName = $route->getActionName();
            $controller = strstr($actionName, '@', true); // Get the controller name
            $groupedRoutes[$controller][] = $route;
        }

        // Generate documentation with one example per controller
        foreach ($groupedRoutes as $controller => $routes) {
            $doc .= "### {$controllerCount}. Routes for: `$controller`\n\n";

            // Include details for each route
            foreach ($routes as $index => $route) {
                $number = $index + 1; // Start numbering from 1
                $doc .= "- **Route {$number}**: `{$route->uri}`\n";
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
            $controllerCount++; // Increment the controller counter
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
        $modelsCount = 1; // Initialize a counter for models
        $doc = "## Models Documentation\n\n";
        $doc .= "The following models are defined in the `app/Models` directory:\n\n";

        foreach ($models as $model) {
            $modelName = pathinfo($model, PATHINFO_FILENAME);
            $doc .= " `$modelName` | ";
        }

        foreach ($models as $model) {
            if (pathinfo($model, PATHINFO_EXTENSION) === 'php') {
                $modelName = pathinfo($model, PATHINFO_FILENAME);
                $doc .= "\n### **$modelsCount**: `$modelName`\n";

                // Reflection for additional details
                $reflection = new \ReflectionClass("App\\Models\\$modelName");

                // List fillable fields
                $fillable = $reflection->getDefaultProperties()['fillable'] ?? [];
                $doc .= "  - **Fillable Fields**:\n";
                $doc .= "```php\n";
                $fillables = json_encode($fillable, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                $doc .= "protected \$fillable = {$fillables};\n";
                $doc .= "```\n";

                // Separate methods & relationships
                $definedMethods = [];
                $relationships = [];

                foreach ($reflection->getMethods() as $method) {
                    if ($method->class === $reflection->getName()) {
                        $excludedMethods = [
                            'forceDelete', 'performDeleteOnModel', 'bootSoftDeletes',
                            'initializeSoftDeletes', 'forceDeleteQuietly', 'runSoftDelete',
                            'restore', 'restoreQuietly', 'trashed', 'softDeleted',
                            'restoring', 'restored', 'forceDeleting', 'forceDeleted',
                            'isForceDeleting', 'getDeletedAtColumn', 'getQualifiedDeletedAtColumn',
                            'factory', 'newFactory'
                        ];

                        if (!in_array($method->getName(), $excludedMethods)) {
                            try {
                                $instance = $reflection->newInstance();
                                $result = $method->invoke($instance);

                                if ($result instanceof Relation) {
                                    $relationships[] = [
                                        'name' => $method->getName(),
                                        'type' => class_basename($result),
                                        'related' => class_basename($result->getRelated())
                                    ];
                                } else {
                                    $definedMethods[] = $method->getName();
                                }
                            } catch (\Throwable $e) {
                                $definedMethods[] = $method->getName();
                            }
                        }
                    }
                }

                // Add defined methods
                $doc .= "  - **Methods**:\n";
                if (!empty($definedMethods)) {
                    foreach ($definedMethods as $methodName) {
                        $doc .= "    - `{$methodName}()`\n";
                    }
                } else {
                    $doc .= "    - _(none)_\n";
                }

                // Add relationships
                $doc .= "  - **Relationships**:\n";
                if (!empty($relationships)) {
                    foreach ($relationships as $rel) {
                        $doc .= "    - `{$rel['name']}()` → {$rel['type']}({$rel['related']})\n";
                    }
                } else {
                    $doc .= "    - _(none)_\n";
                }

                // Mermaid diagram for relationships
                if (!empty($relationships)) {
                    $doc .= "\n#### Relationship Diagram\n";
                    $doc .= "```mermaid\n";
                    $doc .= "graph TD\n";
                    foreach ($relationships as $rel) {
                        $doc .= "    {$modelName} -->|{$rel['type']}| {$rel['related']}\n";
                    }
                    $doc .= "```\n";
                }

                $doc .= "\n---\n\n";
                $modelsCount++; // Increment the model counter
                $doc .= "\n";
            }
        }

        return $doc;
    }

    protected function getSimpleModelDiagram($path)
    {
        $modelsPath = $path . '/app/Models';
        $models = array_diff(scandir($modelsPath), ['.', '..']);
        $edges = [];
        $nodes = [];
        $processedRelations = [];

        foreach ($models as $modelFile) {
            if (pathinfo($modelFile, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
            $fullClass = "App\\Models\\$modelName";

            if (!class_exists($fullClass)) {
                continue;
            }

            try {
                $reflection = new \ReflectionClass($fullClass);
                if ($reflection->isAbstract()) {
                    continue;
                }

                $instance = $reflection->newInstance();

                $nodes[$modelName] = true; // Track seen classes

                foreach ($reflection->getMethods() as $method) {
                    if ($method->class !== $fullClass) continue;

                    try {
                        $result = $method->invoke($instance);
                        if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                            $related = class_basename(get_class($result->getRelated()));
                            $relationType = class_basename($result);

                            // Avoid duplicate relationships
                            $signature = $modelName . '-' . $related . '-' . $relationType;
                            $reverseSignature = $related . '-' . $modelName . '-' . $relationType;

                            if (in_array($signature, $processedRelations) || in_array($reverseSignature, $processedRelations)) {
                                continue;
                            }
                            $processedRelations[] = $signature;

                            // Determine arrow style
                            $arrow = match ($relationType) {
                                'BelongsTo' => " -->|1 to 1| ",
                                'HasOne' => " -->|1 to 1| ",
                                'HasMany' => " -->|1 to *| ",
                                'BelongsToMany' => " -->|* to *| ",
                                default => " --> ",
                            };

                            $edges[] = "    {$modelName}{$arrow}{$related}";
                            $nodes[$related] = true; // Ensure target also rendered
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Generate Mermaid output
        $diagram = "## Simple Eloquent Class Diagram\n\n";
        $diagram .= "```mermaid\n";
        $diagram .= "graph TD\n";
        $diagram .= "%% Simple class-to-class relationships\n";

        // Ensure all nodes are declared so layout is tight
        foreach (array_keys($nodes) as $className) {
            $diagram .= "    {$className}\n";
        }

        // Add edges
        foreach ($edges as $edge) {
            $diagram .= $edge . "\n";
        }

        $diagram .= "```\n";

        return $diagram;
    }

    protected function getModelsSchemaDiagram($path)
    {
        $modelsPath = $path . '/app/Models';
        $models = array_diff(scandir($modelsPath), ['..', '.']);
        $allRelationships = [];

        foreach ($models as $model) {
            if (pathinfo($model, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $modelName = pathinfo($model, PATHINFO_FILENAME);
            $fullClass = "App\\Models\\$modelName";

            if (!class_exists($fullClass)) {
                continue;
            }

            try {
                $reflection = new \ReflectionClass($fullClass);

                // Create instance (skip if abstract)
                if ($reflection->isAbstract()) {
                    continue;
                }

                $instance = $reflection->newInstance();

                foreach ($reflection->getMethods() as $method) {
                    if ($method->class === $reflection->getName()) {
                        try {
                            $result = $method->invoke($instance);

                            if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                                $relatedModel = get_class($result->getRelated());
                                $relatedName = class_basename($relatedModel);
                                $relationType = class_basename($result);

                                $allRelationships[] = [
                                    'from' => $modelName,
                                    'to' => $relatedName,
                                    'type' => $relationType,
                                ];
                            }
                        } catch (\Throwable $e) {
                            // Skip methods that can't be invoked
                            continue;
                        }
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Now generate the unified Mermaid diagram
        $diagram = "## Database Schema Diagram\n\n";
        $diagram .= "```mermaid\n";
        $diagram .= "graph TD\n";

        foreach ($allRelationships as $rel) {
            $diagram .= "    {$rel['from']} -->|{$rel['type']}| {$rel['to']}\n";
        }

        $diagram .= "```\n";

        return $diagram;
    }


    protected function getDatabaseSchemaAdvanced($path)
    {
        $modelsPath = $path . '/app/Models';
        $models = array_diff(scandir($modelsPath), ['.', '..']);

        $nodes = [];
        $edges = [];
        $processedRelations = [];

        foreach ($models as $modelFile) {
            if (pathinfo($modelFile, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
            $fullClass = "App\\Models\\$modelName";

            if (!class_exists($fullClass)) {
                continue;
            }

            try {
                $reflection = new \ReflectionClass($fullClass);

                if ($reflection->isAbstract()) {
                    continue;
                }

                $instance = $reflection->newInstance();
                $fillable = $reflection->getDefaultProperties()['fillable'] ?? [];

                // Build node for model with fields
                $label = "{$modelName}\n";
                if (!empty($fillable)) {
                    foreach ($fillable as $field) {
                        $label .= "+ {$field}\n";
                    }
                } else {
                    $label .= "(none)\n";
                }

                // Escape double quotes inside labels if any (rare)
                $label = str_replace('"', '\"', $label);

                // Now inject into Mermaid node
                $node = "    {$modelName}[\"{$label}\"]\n";

                $nodes[$modelName] = $node;

                // Detect relationships
                foreach ($reflection->getMethods() as $method) {
                    if ($method->class !== $fullClass) continue;

                    try {
                        $result = $method->invoke($instance);
                        if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                            $related = class_basename(get_class($result->getRelated()));
                            $relationType = class_basename($result);

                            $relationSignature = $modelName . '-' . $related . '-' . $relationType;

                            // Skip if reverse already defined (e.g., A --> B and B --> A)
                            if (in_array($relationSignature, $processedRelations) || in_array($related . '-' . $modelName . '-' . $relationType, $processedRelations)) {
                                continue;
                            }

                            $processedRelations[] = $relationSignature;

                            // Relationship arrows based on type
                            $arrow = match ($relationType) {
                                'BelongsTo' => " -->|1 to 1| ",
                                'HasOne' => " -->|1 to 1| ",
                                'HasMany' => " -->|1 to *| ",
                                'BelongsToMany' => " -->|* to *| ",
                                default => " --> ",
                            };

                            $edges[] = "    {$modelName}{$arrow}{$related}";
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Generate final Mermaid diagram
        $diagram = "## Database Schema Advanced\n\n";
        $diagram .= "```mermaid\n";
        $diagram .= "%% Mermaid class diagram layout\n";
        $diagram .= "graph LR\n";
        $diagram .= "classDef bigText fill:#f9f,stroke:#333,stroke-width:2px,font-size:18px;\n";

        foreach ($nodes as $node) {
            $diagram .= $node;
        }

        foreach ($edges as $edge) {
            $diagram .= $edge . "\n";
        }

        $diagram .= "```\n";

        return $diagram;
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
                $doc .= "### **{$controllerCount}. Controller**: `$controllerName`\n";

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
        $doc = "## Views Documentation\n\n";

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
        $insights = "### View: `{$filePath}`\n\n";

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

    protected function getDocumentationSummary()
    {
        $summary  = "## 📘 Documentation Summary\n\n";
        $summary .= "This documentation provides a complete overview of the Laravel application, including every major part of the system. ";
        $summary .= "It begins with the **Project Requirements and Installation** section, explaining the framework version, dependencies, and setup steps needed to run the project locally or on a server. ";
        $summary .= "The **Project Structure** section then describes the application's folder organization and the role of each key directory such as `app/`, `routes/`, and `resources/`. ";
        $summary .= "Following that, the **Routes Documentation** lists all defined routes, including their HTTP methods, URIs, controllers, and middleware, offering a clear map of the application’s endpoints. ";
        $summary .= "The **Simple Eloquent Class Diagram** provides a visual overview of how the main models relate to one another, while the **Database Schema Diagram** and **Database Schema Advanced** sections present Mermaid-based ER diagrams that represent the database structure and relationships in detail. ";
        $summary .= "Next, the **Models Documentation** section outlines each Eloquent model, showing its fillable fields, methods, and defined relationships such as HasMany, BelongsTo, and HasOne. ";
        $summary .= "The **Controllers Documentation** section details all controller classes and their key methods, describing how each one interacts with models, services, and views to implement business logic. ";
        $summary .= "The **Views Documentation** lists Blade templates and layouts that define the front-end presentation of the application. ";
        $summary .= "The **Migrations Documentation** explains each migration file, including table definitions and schema changes, followed by the **Seeders Documentation**, which covers the seeders and the sample data they populate. ";
        $summary .= "The **Environment Configuration** section provides information about `.env` settings and configuration variables used for databases, caching, mail, and third-party integrations. ";
        $summary .= "The **Tests Documentation** summarizes unit, feature, and integration tests that ensure the reliability of the system, while the **Middleware Documentation** describes all middleware classes that manage authentication, authorization, and request filtering. ";
        $summary .= "Overall, this documentation provides a structured, visual, and detailed overview of the entire project to assist developers, maintainers, and contributors in understanding the full architecture and functionality of the system.\n";

        return $summary;
    }


}
