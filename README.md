# Blitzy ⚡

Blitzy is a lightweight package for Laravel that automates test generation. It is designed to speed up the testing process and make developers' lives easier. This is the very first version of this package, and improvements will be implemented soon.

## Installation
```bash
composer require Danon910/blitzy
```

## What does the package do?
This package generates `Test` and `Trait` files in the `tests` folder with the correct namespace and a simple structure, ready for writing real working tests.

## Usage
### Generate smoke test for specific Controller
```bash
php artisan blitzy:generate "App\Http\Controllers\PostController" --type=smoke
```

## Configuration
```bash
php artisan vendor:publish --provider="Danon910\blitzy\BlitzyServiceProvider"
```
Blitzy is ready to use out of the box, but if you want to customize its behavior, you can edit the configuration file located at `config/blitzy.php`.

---

We hope Blitzy accelerates and simplifies your testing workflow in Laravel. Happy coding!
