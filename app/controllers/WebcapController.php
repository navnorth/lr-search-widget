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
		if(preg_match('#\.(doc|docx|pdf|xls|xlsx|ppt|pptx)$#', parse_url($url, PHP_URL_PATH), $matches))
		{
			$file = storage_path('screencaps/documents/'.substr($matches[1], 0, 3).'/full.jpg');
		}
		else
		{
			$file = $this->_getScreenshotFile($url);
		}


		if(!$file)
		{
			App::abort('404', 'Failed to render screenshot');
		}


		if($size)
		{
			$file = $this->_resizeScreenshot($file, $size);
		}

		if(file_exists($file))
		{
			$headers = array(
				'content-type' => 'image/jpeg',
				'expires' => gmdate ("D, d M Y H:i:s", time() + 7200),
                'header' => 'cache-control: max-age=7200, must-revalidate',
			);

			return Response::make(file_get_contents($file), 200, $headers);
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

		// split hashes into separate folders xxxxyyyyzzzzzzzz => /xxxx/yyyy/zzzzzzzz
		preg_match('#^(.{4})(.{4})(.+?)$#', $hash, $matches);

		$path = storage_path('screencaps/'.implode('/', array_slice($matches, 1)).'/');

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

			$output = exec(implode(' ', $params), $lines, $val);
			/*var_dump(implode(' ', $params));
			dd(array($lines, $val));*/
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
