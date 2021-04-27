<?php

namespace Ebalo\EasyCRUD;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait EasyCRUD
{
	protected array $rules = [];

	/**
	 * Perform a basic crud operation.
	 * It will do in this order:
	 * - validate the provided request with the given rules;
	 *      - return back with the list of validation errors if any
	 * - run the given method on the given model with the validated parameters
	 * - redirect to the successful_redirect route with a "state" flag with a "confirmed" value
	 *
	 * @param Request|null $request Request to start validate
	 * @param string|object $model Model on which to execute the action
	 * @param string $method Action to execute on the model
	 * @param string $successful_redirect Route to redirect if everything is ok
	 * @return RedirectResponse
	 */
	public function easyCrud(Request|null $request, string|object $model, string $method, string $successful_redirect): RedirectResponse
	{
		return easyCrud(
			$this,
			$request,
			$method === "update" ? dropUniquenessRule($this->rules) : $this->rules,
			$model,
			$method,
			$successful_redirect
		);
	}

	/**
	 * Preformatted shortcut for the store procedure, it routes the formatted request to easyCrud
	 *
	 * @param Request $request
	 * @param string $model
	 * @param string $successful_redirect
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
	 * Preformatted shortcut for the update procedure, it routes the formatted request to easyCrud
	 *
	 * @param Request $request
	 * @param object $model
	 * @param string $successful_redirect
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
	 * Preformatted shortcut for the delete procedure, it routes the formatted request to easyCrud
	 *
	 * @param object $model
	 * @param string $successful_redirect
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
