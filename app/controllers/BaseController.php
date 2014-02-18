<?php

class BaseController extends Controller {

	protected $layout = 'layouts.main';

	protected $viewPrefix = '';

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function _defaultView($view, $parameters = array())
	{
		return $this->layout->with('content', View::make($this->viewPrefix.$view, $parameters));
	}

}
