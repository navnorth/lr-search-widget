<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WidgetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('widget', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('widget_id');
			$table->string('name');
			$table->string('widget_key')->unique();
			$table->text('widget_settings');

			$table->integer('api_user_id')->unsigned();

			$table->foreign('api_user_id')->references('api_user_id')->on('api_user');

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
