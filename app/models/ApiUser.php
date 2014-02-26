<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiUser extends Eloquent implements UserInterface, RemindableInterface {

	protected $primaryKey = 'api_user_id';

	protected $softDelete = true;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'api_user';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'api_key');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->email;
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}


	/*
		Helpers
	 */

	public function display_name()
	{
		return $this->firstname ?: $this->email;
	}

	public function full_name()
	{
		return trim($this->firstname.' '.$this->lastname);
	}

	/*
		Relationships
	 */

	public function searchFilters()
	{
		return $this->hasMany('SearchFilter');
	}

	public function widgets()
	{
		return $this->hasMany('Widget');
	}

}
