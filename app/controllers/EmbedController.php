<?php

class EmbedController extends \BaseController {

    protected $viewPrefix = 'embed.';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
        return $this->_defaultView('index');
	}


	public function getTest()
	{
		return $this->_defaultView('test');
	}

	public function getTemplate()
	{
		$callback = Input::get('jsonp');

		$view = View::make('embed.template');

		if($callback)
		{
			$headers = array(
				'content-type' => 'application/javascript'
			);

			return Response::make($callback.'('.json_encode($view->render()).')', 200, $headers);
		}
		else
		{
			return $view;
		}
	}

	public function getWidget($apiKey)
	{
		$vars = array(
			'api_key' => $apiKey,
			'domain' => URL::to('/'),
			'production' => Config::get('app.production', true),
		);

		$buildVersion = Navnorth\LrPublisher\VersionControl::getBuildVersion();

		$content = 'window.LRWidget = '.json_encode($vars).";\n";
		$content .= 'window.LRWidgetBuildVersion = '.json_encode($buildVersion).";\n\n";

		$assets = array(
			'/js/require.js',
			'/js/embed_config.js',
		);

		foreach($assets as $a)
		{
			$content .= file_get_contents(public_path($a));
		}

		$headers = array(
			'content-type' => 'application/javascript',
		);

		return Response::make($content, 200, $headers);
	}

}
