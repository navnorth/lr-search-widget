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

		$this->_applyFilterSettings($filter);

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
		if($filter = $this->_loadSearchFilter($id))
		{
			return $this->_defaultView('show', array('filter' => $filter));
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

		if($filter = $this->_loadSearchFilter($id))
		{
			return $this->_defaultView('edit', array('filter' => $filter));
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
		if($filter = $this->_loadSearchFilter($id))
		{
			$this->_applyFilterSettings($filter);

			$filter->save();

			return Redirect::to($filter->link());
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

	protected function _applyFilterSettings(SearchFilter $filter)
	{
		$filter->name = Input::get('name');
		$filter->filter_settings = Input::only(array(
			SearchFilter::FILTER_INCLUDE,
			SearchFilter::FILTER_EXCLUDE,
			SearchFilter::FILTER_DISCOURAGE,
			SearchFilter::FILTER_WHITELISTED_ONLY,
			SearchFilter::FILTER_INCLUDE_BLACKLISTED,
		));
	}

	protected function _loadSearchFilter($id)
	{
		$filter = SearchFilter::find($id);

		if(Auth::user()->api_user_id == $filter->api_user_id)
		{
			return $filter;
		}
		else
		{
			return null;
		}
	}

}
