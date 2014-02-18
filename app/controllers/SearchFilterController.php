<?php

class SearchFilterController extends \BaseController {

	protected $viewPrefix = 'search_filters.';

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
		$filter = new SearchFilter();

		$filter->name = Input::get('name');
		$filter->filter_settings = Input::only(array('include', 'exclude', 'exclude_non_whitelisted', 'include_blacklisted'));

		$filter->filter_key = str_random(10);
		$filter->api_user_id = Auth::user()->api_user_id;

		$filter->save();

		return Redirect::to($filter->link());

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$filter = SearchFilter::find($id);

		if(Auth::user()->api_user_id == $filter->api_user_id)
		{
			SearchFilter::where('filter_key', 'Ulx230mcyc')->where('api_user_id', Auth::user()->api_user_id)->first();

			return $this->_defaultView('show', compact('filter'));
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
		$filter = SearchFilter::find($id);

		if(Auth::user()->api_user_id == $filter->api_user_id)
		{
			return $this->_defaultView('edit', compact('filter'));
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
		//
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

}
