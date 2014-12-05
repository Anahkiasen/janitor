<?php
namespace Janitor;

use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
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
		$this->registerCoreClasses();
		$this->registerJanitorClasses();

		if ($this->app->bound('artisan')) {
			$this->commands(array(
				'Janitor\Commands\CleanViews',
				'Janitor\Commands\CleanRoutes',
			));
		}
	}

	/**
	 * Register third party services
	 */
	protected function registerCoreClasses()
	{
		$this->app->bindIf('files', 'Illuminate\Filesystem\Filesystem');

		$this->app->bindIf('config', function ($app) {
			$fileloader = new FileLoader($app['files'], __DIR__.'/../config');

			return new Repository($fileloader, 'config');
		}, true);

		$this->app->config->package('anahkiasen/janitor', __DIR__.'/../config');
	}

	/**
	 * Register Janitor services
	 */
	protected function registerJanitorClasses()
	{
		$this->app->singleton('Janitor\Codebase', function ($app) {
			$codebase = new Codebase($app['path'], $app['config']->get('janitor::ignored'));
			if ($app->bound('router')) {
				$codebase->setRoutes($app['router']->getRoutes());
			}

			return $codebase;
		});
	}
}
