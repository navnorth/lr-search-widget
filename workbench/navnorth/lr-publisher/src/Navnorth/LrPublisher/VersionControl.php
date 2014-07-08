<?php namespace Navnorth\LrPublisher;

class VersionControl
{
    static $instance = null;

    protected $version;

    public function __construct()
    {
        $this->version = substr(@file_get_contents(base_path('.git/refs/heads/master')) ?: '1234567890', 0, 10);
    }

    public static final function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new VersionControl();
        }

        return self::$instance;
    }

    public static function getBuildVersion()
    {
        return self::getInstance()->version;

    }
}
