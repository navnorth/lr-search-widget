<?php

class WebcapController extends BaseController
{

	protected function esClient()
    {
        $config = Config::get('search');

        return ElasticSearch\Client::connection($config);
    }

	public function getScreencapById($id, $size = null)
	{
		$doc = $this->esClient()->get($id);

		return $this->_serveScreenshot($doc['url'], $size);
	}

	public function getIndex()
	{
		$url = Input::get('url');
		$size = Input::get('size', null);

		if(!$url)
		{
			App::abort('400', 'Requires `url` query parameter');
		}

		return $this->_serveScreenshot($url, $size);
	}

	protected function _serveScreenshot($url, $size)
	{
		$file = $this->_getScreenshotFile($url);

		if($size)
		{
			$file = $this->_resizeScreenshot($file, $size);
		}

		if(file_exists($file))
		{
			return Response::make(file_get_contents($file), 200, array('content-type' => 'image/jpeg'));
		}
		else
		{
			return App::abort(404);
		}

	}

	protected function _hashUrl($url)
	{
		return hash('md5', $url);
	}

	protected function _getScreenshotFile($url)
	{
		$hash = $this->_hashUrl($url);

		$path = storage_path('screencaps/'.$hash.'/');

		if(!file_exists($path))
		{
			mkdir($path, 0777, true);
		}

		if(!file_exists($file = $path.'full.jpg') || Input::get('reload'))
		{
			// create screenshot

			$params = array(
				/*'xvfb-run',
				'--auto-servernum',
				'--server-num=1',*/
				'/usr/local/bin/python',
				app_path('executables/screenshot.py'),
				escapeshellarg($url),
				$file,
			);

			$lines = array();
			$val = 0;

			//dd(implode(' ', $params));
			$output = exec(implode(' ', $params), $lines, $val);
		}

		return $file;
	}

	protected function _resizeScreenshot($file, $size)
	{
		$path = dirname($file);

		if(!file_exists($resizedFile = $path.'/'.$size.'.jpg'))
		{
			$img = Image::make($file)->resize($size, null, true);

			$img->save($resizedFile);
		}

		return $resizedFile;
	}

}
