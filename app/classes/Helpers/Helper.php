<?php namespace Helpers;

class Helper {

		public static function split_name($name)
		{
				$name = trim($name);
		    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
		    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
		    return array('first_name' => $first_name, 'last_name' => $last_name);
		}
}
