<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOauthToApiUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_user', function ($table) {
			$table->string('oauth_id')->unique();
			$table->string('oauth_type', 10);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('api_user', function ($table) {
			$table->dropColumn('oauth_id');
			$table->dropColumn('oauth_type');
		});
	}

}
