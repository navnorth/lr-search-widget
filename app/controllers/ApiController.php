<?php

class ApiController extends BaseController {

	public $restful = true;

    protected $apiUser = null;

	public function __construct()
    {
        $this->beforeFilter('@filterApiAuth');
        $this->afterFilter('@filterJsonp');
    }

    public function getUserId()
    {
        return $this->apiUser ? $this->apiUser->api_user_id : null;
    }

    public function filterApiAuth($route, $request)
    {

        $apiKeys = array(
            $request->headers->get('X-Api-Key'),
            Input::get('api-key'),
            Input::get('api_key'),
        );

        // find the first relevent key from our different submission means
        $apiKey = current(array_filter($apiKeys));

        $this->apiUser = ApiUser::where('api_key', $apiKey)->first();

        if(!$this->apiUser)
        {
            $this->apiUser = Auth::user();
        }

        if(!$this->apiUser)
        {
            return array('error' => true, 'message' => 'invalid api_key');
        }



        // Redis-based usage stats

        $db = Redis::connection();

        $userId = $this->getUserId();

        $db->pipeline(function($db) use ($userId) {

            $path = Request::path();

            $d = date('Ymd');

            $key = 'usage';

            // global usage specifics
            $db->hincrby($key, $path, 1);
            $db->hincrby($key.':'.$d, $path, 1);


            $key = 'usage:'.$userId;

            // user usage specifics
            $db->hincrby($key, $path, 1);
            $db->hincrby($key.':'.$d, $path, 1);
        });

    }

    public function filterJsonp($route, $request, $response)
    {
        if($response->headers->get('content-type') === 'application/json')
        {
            if($callback = Input::get('jsonp'))
            {
                // sanitize callback (http://stackoverflow.com/questions/2777021/do-i-need-to-sanitize-the-callback-parameter-from-a-jsonp-call)
                $callback = preg_replace("/[^][.\\'\\\"_A-Za-z0-9]/", '', $callback);

                // interject jsonp data into our json response
                $response->header('content-type', 'application/javascript');
                $response->setContent($callback.'('.$response->getContent().')');
            }
        }
    }

	/**
     * Catch-all method for requests that can't be matched.
     *
     * @param  string    $method
     * @param  array     $parameters
     * @return Response
     */
    public function missingMethod($parameters = array())
    {
        $action = head($parameters);

        $method = strtolower(Request::getMethod());

        /*
            if the main action is a number or semi-colon separated number list,
            default to $method_index with the id(s) passed as the parameter
        */
        if(is_numeric($action) || preg_match('#^(\d+)(;\d+)+$#', $action))
        {
            return $this->{$method.'Index'}($action, $parameters);
        }

        return Response::error('404');
    }

    /* Helper Functions */
    protected function _applyCacheControl($r, $expires = 86400 /* 1 day */)
    {
        $r->header('expires',  gmdate ("D, d M Y H:i:s", time() + $expires));
        $r->header('cache-control', 'max-age='.$expires.', must-revalidate');

        return $r;
    }
}
