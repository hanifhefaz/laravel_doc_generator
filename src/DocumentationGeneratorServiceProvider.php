<?php

namespace HanifHefaz\DocumentationGenerator;

use Illuminate\Support\ServiceProvider;
use HanifHefaz\DocumentationGenerator\GenerateDocumentationCommand;

class DocumentationGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any bindings or services if necessary
    }

    public function boot()
    {
        // Define the command
        $this->commands([GenerateDocumentationCommand::class]);
    }
}
