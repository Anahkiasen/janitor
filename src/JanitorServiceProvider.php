<?php
namespace Janitor;

use Illuminate\Support\ServiceProvider;

// Define DS
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

class JanitorServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->config->package('anahkiasen/janitor', __DIR__.'/../config');

		// Define codebase
		$this->app->singleton('Janitor\Codebase', function ($app) {
			$codebase = new Codebase($app['path'], $app['config']->get('janitor::ignored'));
			if ($app->bound('router')) {
				$codebase->setRoutes($app['router']->getRoutes());
			}

			return $codebase;
		});

		$this->commands(array(
			'Janitor\Commands\CleanViews',
			'Janitor\Commands\CleanRoutes',
		));
	}
}