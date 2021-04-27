# Laravel easy-crud

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ebalo/easycrud.svg?style=flat-square)](https://packagist.org/packages/ebalo/easycrud)
[![Total Downloads](https://img.shields.io/packagist/dt/ebalo/easycrud.svg?style=flat-square)](https://packagist.org/packages/ebalo/easycrud)

This package was created to avoid the repetitive task of creating standard crud endpoints in Laravel applications, the ultimate goal
is to let you create your standard crud endpoint in less than a minute without having to worry about all the repeating tasks.

## Installation

You can install the package via composer:

```bash
composer require ebalo/easycrud
```

Then proceed with the package initialization with:

```bash
php artisan easy-crud:install
```

In case you want to manually install the trait for all your controller or only install it for some of them you should:
- Incude `use Ebalo\EasyCRUD\EasyCRUD;` in the controller that needs the trait
- Include `use EasyCRUD;` inside the controller body

## Usage

The package provide two method to access all the function:
- The preferred one is through the usage of the EasyCRUD trait, inserted automatically on your main controller when 
  completing installation
- The second method is through the usage of the helper functions

### Usage through trait
The trait exposes four main functions, these are:
- `easyCrud` It performs the basic crud operation, it is the most flexible method and is the base for all the other 
  methods.
- `easyStore` It is specifically designed to store a new model instance.
- `easyUpdate` It is specifically designed to update an existing model instance.
- `easyDelete` It is specifically designed to delete an existing model instance.

The store and update procedure relay on the `rules` array to retrieve the parameter from the request.
The `rules` array is a standard laravel validation array as the ones used in the `Validator::create` method or in the
standard `validate` method of the controller class.

#### Example usage
```php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// ...

class CategoryController extends Controller
{
    // Validation rules array, inherited from the EasyCRUD trait
    protected array $rules = [
        "name" => "required|string|max:255|unique:categories,name",
        "icon" => "required|string",
        // ...
    ];
    
    public function store(Request $request): RedirectResponse
    {
        return $this->easyStore(
            $request,               // Request to validate
            Category::class,        // Model to create
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
    
    public function update(Request $request, Category $category): RedirectResponse
    {
        return $this->easyUpdate(
            $request,               // Request to validate
            $category,              // Model to update
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
    
    public function destroy(Category $category): RedirectResponse
    {
        return $this->easyDelete(
            $category,              // Model to update
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
    
    // ...
}
```
You may have noted that the rules has a unique check on the `name` parameter, this check is automatically dropped in the
update phase in order to let you easily update the model without any warning.
<br>
All the previous methods can also be written with the usage of the standard and more flexible `easyCrud` function, 
see the following example.

```php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// ...

class CategoryController extends Controller
{
    // Validation rules array, inherited from the EasyCRUD trait
    protected array $rules = [
        "name" => "required|string|max:255|unique:categories,name",
        "icon" => "required|string",
        // ...
    ];
    
    public function store(Request $request): RedirectResponse
    {
        return $this->easyCrud(
            $request,               // Request to validate
            Category::class,        // Model to create
            "create",               // Creation function
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
    
    // ...
}
```
You see that the `easyCrud` is not that different from the previous examples.

### Usage through helpers
There are four helper functions, these are:
- `easyCrud` It performs the basic crud operation, it is the most flexible method and is the base for all the other
  methods.
- `easyStore` It is specifically designed to store a new model instance.
- `easyUpdate` It is specifically designed to update an existing model instance.
- `easyDelete` It is specifically designed to delete an existing model instance.

The store and update procedure relay on the `rules` array to retrieve the parameter from the request.
The `rules` array is a standard laravel validation array as the ones used in the `Validator::create` method or in the
standard `validate` method of the controller class.
<br>
As you can see the name are exactly the name of the functions available with the trait, this is done in order to let you 
remember only one function name.

#### Example usage
The example usage is a bit more difficult and tedious than the previous one, check the following examples of the same 
methods implemented previously.

```php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// ...

class CategoryController extends Controller
{
    // Note the that $rules now is marked as private
    private array $rules = [
        "name" => "required|string|max:255|unique:categories,name",
        "icon" => "required|string",
        // ...
    ];

    public function store(Request $request): RedirectResponse
    {
        return easyStore(
            $this,                  // Instance of the caller class
            $request,               // Request to validate
            $this->rules,           // Array of validation rules
            Category::class,        // Model to create
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        return easyUpdate(
            $this,                  // Instance of the caller class
            $request,               // Request to validate
            $this->rules,           // Array of validation rules
            $category,              // Model to update
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }

    public function destroy(Category $category): RedirectResponse
    {
        return easyDelete(
            $category,              // Model to delete
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
}
```

All the previous methods can also be written with the usage of the standard and more flexible `easyCrud` function,
see the following example.

```php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// ...

class CategoryController extends Controller
{
    // Validation rules array, inherited from the EasyCRUD trait
    protected array $rules = [
        "name" => "required|string|max:255|unique:categories,name",
        "icon" => "required|string",
        // ...
    ];
    
    public function store(Request $request): RedirectResponse
    {
        return easyCrud(
            $this,                  // Instance of the caller class
            $request,               // Request to validate
            $this->rules,           // Array of validation rules
            Category::class,        // Model to create
            "create",               // Creation function
            "categories-index"      // Redirect if validation and creation ends successfully
        );
    }
    
    // ...
}
```

## Additional functionality
As creating CRUD routes is almost always a copy-paste of previously created code, the package is shipped with a 
`CRUD` helper that can be easily called from inside your routes.
See the following examples.

```php
use App\Http\Controllers\CategoryController;
// ...

CRUD(
    "categories",                   // Route path prefix
    CategoryController::class,      // Controller class to call
    "category",                     // Route name prefix
    "category"                     // Parameter name for the routes that requires one
);
// ...
```

The above code works exactly like the following one.

```php
use App\Http\Controllers\CategoryController;
// ...

Route::prefix("/categories")->group(function() {
     Route::get("/", [CategoryController::class, "index"])->name("category-list");
     Route::get("/{category}", [CategoryController::class, "show"])->name("category-show");
     
     Route::get("/create", [CategoryController::class, "create"])->name("category-create");
     Route::post("/create", [CategoryController::class, "store"])->name("category-store");
     
     Route::get("/edit/{category}", [CategoryController::class, "edit"])->name("category-edit");
     Route::put("/edit/{category}", [CategoryController::class, "update"])->name("category-update");
     
     Route::delete("/delete/{category}", [CategoryController::class, "destroy"])->name("category-delete");
});
// ...
```

If you don't want to generate all the predefined crud routes you can pass also an additional associative array like the 
following example.

```php
use App\Http\Controllers\CategoryController;
// ...

CRUD(
    "categories",                   // Route path prefix
    CategoryController::class,      // Controller class to call
    "categories",                   // Route name prefix
    "category",                     // Parameter name for the routes that requires one
    [                               // Array of crud functionalities to register
        "create" => true,           // Register the creation endpoints
        "read" => false,            // Don't register the reading endpoints
        "update" => false,          // Don't register the updating endpoints
        "delete" => true           // Don't register the deletion endpoints
    ]
);
// ...
```

The above code is the same as the following.

```php
use App\Http\Controllers\CategoryController;
// ...

Route::prefix("/categories")->group(function() {     
     Route::get("/create", [CategoryController::class, "create"])->name("categories-create");
     Route::post("/create", [CategoryController::class, "store"])->name("categories-store");
     
     Route::delete("/delete/{category}", [CategoryController::class, "destroy"])->name("categories-delete");
});
// ...
```

There is also a last additional way to call the `CRUD` function, it gives you the possibility to change the function to
call once the route is executed, see this last example.

```php
use App\Http\Controllers\CategoryController;
// ...

CRUD(
    "categories",                   // Route path prefix
    CategoryController::class,      // Controller class to call
    "categories",                   // Route name prefix
    "category",                     // Parameter name for the routes that requires one
    [                               // Array of crud functionalities to register
        "create" => true,           // Register the creation endpoints
        "read" => false,            // Don't register the reading endpoints
        "update" => false,          // Don't register the updating endpoints
        "delete" => false            // Don't register the deletion endpoints
    ],
    $functions = [
        "list" => "list_function",
        "read" => "read_function",
        "create" => "creation_function",
        "store" => "store",
        "edit" => "edit",
        "update" => "update",
        "delete" => "destroy"
    ]
);
// ...
```

The above code is the same as the following.

```php
use App\Http\Controllers\CategoryController;
// ...

Route::prefix("/categories")->group(function() {     
     Route::get("/create", [CategoryController::class, "creation_function"])->name("categories-create");
     Route::post("/create", [CategoryController::class, "store"])->name("categories-store");
});
// ...
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please use the issue tracker.

## Credits

-   [ebalo](https://github.com/ebalo55)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
