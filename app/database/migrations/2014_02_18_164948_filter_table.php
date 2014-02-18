<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FilterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('search_filter', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('search_filter_id');
			$table->string('name');
			$table->string('filter_key')->unique();
			$table->text('filter_settings');

			$table->integer('api_user_id')->unsigned();

			$table->foreign('api_user_id')->references('api_user_id')->on('api_user');

			$table->timestamps();
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
