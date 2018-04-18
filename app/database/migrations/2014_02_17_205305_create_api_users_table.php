<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_user', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('api_user_id');
			$table->string('firstname', 50);
			$table->string('lastname', 50);
			$table->string('email', 50)->unique();
			$table->string('password', 50);
			$table->string('organization', 50);
			$table->string('url', 255);
			$table->string('api_key', 200)->unique();
			/*$table->timestamps();*/
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
			$table->softDeletes();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
