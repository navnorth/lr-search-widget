<?php

class WidgetController extends \BaseController {

	protected $viewPrefix = 'widgets.';

	public function __construct()
	{
		$this->beforeFilter('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		return $this->_defaultView('create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$widget = new Widget();

		$this->_applyWidgetSettings($widget);

		$widget->widget_key = str_random(12);
		$widget->api_user_id = Session::get('user')->api_user_id;

		$widget->save();

		return Redirect::to($widget->link());

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if($widget = $this->_loadWidget($id))
		{
			return $this->_defaultView('show', array('widget' => $widget));
		}
		else
		{
			return Redirect::to('/');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

		if($widget = $this->_loadWidget($id))
		{
			return $this->_defaultView('edit', array('widget' => $widget));
		}
		else
		{
			return Redirect::to('/');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if($widget = $this->_loadWidget($id))
		{
			$this->_applyWidgetSettings($widget);

			$widget->save();

			return Redirect::to($widget->link());
		}
		else
		{
			return Redirect::to('/');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	protected function _applyWidgetSettings(Widget $widget)
	{
		$widget->name = Input::get('name');
		$widget->widget_settings = Input::only(array_keys(Widget::$DEFAULT_WIDGET_SETTINGS));
	}

	protected function _loadWidget($id)
	{
		$widget = Widget::find($id);

		if(Session::get('user')->api_user_id == $widget->api_user_id)
		{
			return $widget;
		}
		else
		{
			return null;
		}
	}

}
