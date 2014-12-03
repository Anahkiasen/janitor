<?php
namespace Janitor;

use Illuminate\Support\ServiceProvider;

class JanitorServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->commands(array(
			'Janitor\Commands\CleanViews',
		));
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('anahkiasen/janitor');
	}
}
