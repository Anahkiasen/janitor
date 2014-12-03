<?php
namespace Janitor;

use Illuminate\Support\ServiceProvider;
use Janitor\Entities\Codebase;

class JanitorServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Define codebase
		$this->app->singleton('Janitor\Entities\Codebase', function ($app) {
			return new Codebase($app['path']);
		});

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
