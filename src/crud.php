<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

if(!function_exists("CRUD")) {
    /**
     * Registers the CRUD endpoint for a given controller.
     * It is the same as running the following list of commands:
     * Route::prefix("/categories")->group(function() {
     *      Route::get("/", [CategoryController::class, "index"])->name("categories-list");
     *      Route::get("/{category}", [CategoryController::class, "show"])->name("categories-show");
     *
     *      Route::get("/create", [CategoryController::class, "create"])->name("categories-create");
     *      Route::post("/create", [CategoryController::class, "store"])->name("categories-store");
     *
     *      Route::get("/edit/{category}", [CategoryController::class, "edit"])->name("categories-edit");
     *      Route::put("/edit/{category}", [CategoryController::class, "update"])->name("categories-update");
     *
     *      Route::delete("/delete/{category}", [CategoryController::class, "destroy"])->name("categories-delete");
     * });
     *
     * @param string $path_prefix Route path prefix, all routes will start with this prefix
     * @param string $controller Controller name as retrieved from the ::class method
     * @param string $name_prefix The routes name prefix, used for routes identification
     * @param string $parameter_name Parameter name of the routes which requires one
     * @param array $functionalities An array of the crud functionalities to register, defaults to:
     *  ["create" => true, "read" => true, "update" => true, "delete" => true]
     * @param array $functions Array of function name present on the controller, this will be used to run the chosen method, defaults to:
     *  ["list" => "index", "read" => "show", "create" => "create", "store" => "store", "edit" => "edit", "update" => "update", "delete" => "destroy"]
     * @throws Exception
     */
    function CRUD(
        string $path_prefix,
        string $controller,
        string $name_prefix,
        string $parameter_name,
        array $functionalities = ["create" => true, "read" => true, "update" => true, "delete" => true],
        array $functions = [
            "list" => "index",
            "read" => "show",
            "create" => "create",
            "store" => "store",
            "edit" => "edit",
            "update" => "update",
            "delete" => "destroy"
        ]
    ): void {
        // Validation checks
        if(!Arr::has($functionalities, ["create", "read", "update", "delete"])) {
            throw new
                Exception('The functionalities array must include the "create", "read", "update", "delete" elements.');
        }
        if(!Arr::has($functions, ["list", "read", "create", "store", "edit", "update", "delete"])) {
            throw new
                Exception('The functions array must include the "list", "read", "create", "store", "edit", "update", "delete" elements.');
        }
        if(is_null($path_prefix)) {
            throw new Exception('The prefix cannot be null.');
        }
        if(is_null($controller) && !empty($controller)) {
            throw new Exception('The controller cannot be null or empty.');
        }
        if(is_null($name_prefix) && !empty($name_prefix)) {
            throw new Exception('The name_prefix cannot be null or empty.');
        }
        if(is_null($parameter_name) && !empty($parameter_name)) {
            throw new Exception('The parameter_name cannot be null or empty.');
        }

        // Register the actual routes
        Route::prefix("/$path_prefix")->group(
            function() use($controller, $name_prefix, $parameter_name, $functionalities, $functions) {
                // If reading endpoints are required register them
                if($functionalities["read"]) {
                    Route::get("/", [$controller, $functions["list"]])->name("$name_prefix-index");
                    Route::get("/{{$parameter_name}}", [$controller, $functions["read"]])->name("$name_prefix-show");
                }

                // If creation endpoints are required register them
                if($functionalities["create"]) {
                    Route::get("/create", [$controller, $functions["create"]])->name("$name_prefix-create");
                    Route::post("/create", [$controller, $functions["store"]])->name("$name_prefix-store");
                }

                // If editing endpoints are required register them
                if($functionalities["update"]) {
                    Route::get("/edit/{{$parameter_name}}", [$controller, $functions["edit"]])->name("$name_prefix-edit");
                    Route::put("/edit/{{$parameter_name}}", [$controller, $functions["update"]])->name("$name_prefix-update");
                }

                // If deletion endpoints are required register them
                if($functionalities["delete"]) {
                    Route::delete("/delete/{{$parameter_name}}", [$controller, $functions["delete"]])->name("$name_prefix-delete");
                }
        });
    }
}

if(!function_exists("easyCrud")) {
    /**
     * Perform a basic crud operation.
     * It proceed with the following order:
     * - validate the provided request with the given rules;
     *      - return back with the list of validation errors if any
     * - run the given method on the given model with the validated parameters
     * - redirect to the successful_redirect route with a "state" flag with a "confirmed" value
     *
     * @param object|null $caller The caller object, inside a class this field is populated with $this
     * @param Request|null $request Request object to validate
     * @param array $rules Associative array of laravel rules
     * @param string|object $model Model class or object to work on
     * @param string $method Method to run on the model
     * @param string $successful_redirect Route name to redirect the request if successful
     * @return RedirectResponse
     */
    function easyCrud(object|null $caller, Request|null $request, array $rules, string|object $model, string $method, string $successful_redirect): RedirectResponse
    {
        // Some methods do not provide a request by default, if so don't validate the request
        if(!is_null($request)) {
            // Validate the request against the provided rules
            try {
                $caller->validate($request, $rules);
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors());
            }
        }


        // Retrieve the rules size in order to run the method with or without arguments
        $rules_size = count($rules);

        // If the model is a class call the method statically
        if(gettype($model) === "string") {
            if($rules_size > 0) {
                $model::$method($request->only(array_keys($rules)));
            }
            else {
                $model::$method();
            }
        }
        // If the model is an object call the method dynamically
        else {
            if($rules_size > 0) {
                $model->$method($request->only(array_keys($rules)));
            }
            else {
                $model->$method();
            }
        }

        return redirect()->route($successful_redirect)->with("state", "confirmed");
    }
}

if(!function_exists("easyStore")) {
    /**
     * Create a new instance of the given model with the given parameters.
     * This is a preformatted shortcut for the store procedure, it routes the formatted request to easyCrud.
     *
     * @param object $caller The caller object, inside a class this field is populated with $this
     * @param Request $request Request object to validate
     * @param array $rules Associative array of laravel rules
     * @param string $model Model class to create
     * @param string $successful_redirect Route name to redirect the request if successful
     * @return RedirectResponse
     */
    function easyStore(object $caller, Request $request, array $rules, string $model, string $successful_redirect): RedirectResponse
    {
        return easyCrud($caller, $request, $rules, $model, "create", $successful_redirect);
    }
}

if(!function_exists("easyUpdate")) {
    /**
     * Update an existing instance of the given model with the given parameters.
     * This is a preformatted shortcut for the update procedure, it routes the formatted request to easyCrud.
     *
     * @param object $caller The caller object, inside a class this field is populated with $this
     * @param Request $request Request object to validate
     * @param array $rules Associative array of laravel rules
     * @param object $model Model object to update
     * @param string $successful_redirect Route name to redirect the request if successful
     * @return RedirectResponse
     */
    function easyUpdate(object $caller, Request $request, array $rules, object $model, string $successful_redirect): RedirectResponse
    {
        return easyCrud($caller, $request, dropUniquenessRule($rules), $model, "update", $successful_redirect);
    }
}

if(!function_exists("easyDelete")) {
    /**
     * Delete an existing instance of the given model.
     * This is a preformatted shortcut for the delete procedure, it routes the formatted request to easyCrud.
     *
     * @param object $model Model object to delete
     * @param string $successful_redirect Route name to redirect the request if successful
     * @return RedirectResponse
     */
    function easyDelete(object $model, string $successful_redirect): RedirectResponse
    {
        return easyCrud(null, null, [], $model, "delete", $successful_redirect);
    }
}

if(!function_exists("dropUniquenessRule")) {
    /**
     * Drop all uniqueness rules withing the given array of rules, this function is particularly useful in update
     * phases in order to reuse the same rules array.
     * @param array $rules
     * @return array
     */
    function dropUniquenessRule(array $rules): array {
        $result = preg_replace("/\|unique\:\w+,\w+/", "", json_encode($rules));
        return json_decode($result, true);
    }
}
