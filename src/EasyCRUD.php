<?php

namespace Ebalo\EasyCRUD;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait EasyCRUD
{
	protected array $rules = [];

	/**
	 * Perform a basic crud operation.
	 * It proceed with the following order:
	 * - validate the provided request with the given rules;
	 *      - return back with the list of validation errors if any
	 * - run the given method on the given model with the validated parameters
	 * - redirect to the successful_redirect route with a "state" flag with a "confirmed" value
	 *
	 * @param Request|null $request Request object to validate
	 * @param string|object $model Model class or object to work on
	 * @param string $method Method to run on the model
	 * @param string $successful_redirect Route name to redirect the request if successful
	 * @return RedirectResponse
	 */
	public function easyCrud(Request|null $request, string|object $model, string $method, string $successful_redirect): RedirectResponse
	{
		return easyCrud(
			$this,
			$request,
			$method === "update" ? dropUniquenessRule($this->rules) : ($method === "delete" ? [] : $this->rules),
			$model,
			$method,
			$successful_redirect
		);
	}

	/**
	 * Create a new instance of the given model with the given parameters.
	 * This is a preformatted shortcut for the store procedure, it routes the formatted request to easyCrud.
	 *
	 * @param Request $request Request object to validate
	 * @param string $model Model class to create
	 * @param string $successful_redirect Route name to redirect the request if successful
	 * @return RedirectResponse
	 */
	public function easyStore(Request $request, string $model, string $successful_redirect): RedirectResponse
	{
		return $this->easyCrud(
			$request,
			$model,
			"create",
			$successful_redirect
		);
	}

	/**
	 * Update an existing instance of the given model with the given parameters.
	 * This is a preformatted shortcut for the update procedure, it routes the formatted request to easyCrud.
	 *
	 * @param Request $request Request object to validate
	 * @param object $model Model object to update
	 * @param string $successful_redirect Route name to redirect the request if successful
	 * @return RedirectResponse
	 */
	public function easyUpdate(Request $request, object $model, string $successful_redirect): RedirectResponse
	{
		return $this->easyCrud(
			$request,
			$model,
			"update",
			$successful_redirect,
		);
	}

	/**
	 * Delete an existing instance of the given model.
	 * This is a preformatted shortcut for the delete procedure, it routes the formatted request to easyCrud.
	 *
	 * @param object $model Model object to delete
	 * @param string $successful_redirect Route name to redirect the request if successful
	 * @return RedirectResponse
	 */
	public function easyDelete(object $model, string $successful_redirect): RedirectResponse
	{
		return $this->easyCrud(
			null,
			$model,
			"delete",
			$successful_redirect
		);
	}
}
