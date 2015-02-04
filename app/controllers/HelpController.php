<?php

class HelpController extends \BaseController {

    protected $viewPrefix = 'help.';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        return $this->_defaultView('index');
    }

}
