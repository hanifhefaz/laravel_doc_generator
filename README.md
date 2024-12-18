# Documentation Generator

A Laravel package to generate technical documentation for your Laravel project.

<img src="https://banners.beyondco.de/Laravel%20Documentation%20Generator.png?theme=dark&packageManager=composer+require&packageName=hanifhefaz%2Fdocumentation-generator&pattern=parkayFloor&style=style_1&description=Generate+documentation+for+your+laravel%27s+code&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg&widths=400&heights=400">

## Installation

`composer require hanifhefaz/documentation-generator`

## Usage

Run the following command to generate full documentation:

```bash
php artisan docs:generate path/to/your/project/
```

Run the following command to generate documentation based on exclusions.
You can exclude any directory that you dont want to generate documentation for.

```bash
php artisan docs:generate path/to/your/project --excludeDir=name/of/the/directory
```
After running one of the commands above, you can enter the main information required for the documentation as well as the format of your documentation:

![View](/images/command.png "Commands")

That is it.
