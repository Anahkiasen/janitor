<?php
namespace Janitor;

use Illuminate\Cache\CacheManager;
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
     */
    public function register()
    {
        $this->registerCoreClasses();
        $this->registerJanitorClasses();

        if ($this->app->bound('artisan')) {
            $this->commands([
                'Janitor\Console\Commands\CleanViews',
                'Janitor\Console\Commands\CleanRoutes',
            ]);
        }
    }

    /**
     * Register third party services.
     */
    protected function registerCoreClasses()
    {
        $this->app->bindIf('files', 'Illuminate\Filesystem\Filesystem');

        $this->app->bindIf('config', function ($app) {
            $fileloader = new FileLoader($app['files'], __DIR__.'/../config');

            return new Repository($fileloader, 'config');
        }, true);

        $this->app->bindIf('cache', function ($app) {
            $app['config']['cache.driver'] = 'file';
            $app['config']['cache.path'] = __DIR__.'/../cache';

            return new CacheManager($app);
        });

        $this->app->config->package('anahkiasen/janitor', __DIR__.'/../config');
    }

    /**
     * Register Janitor services.
     */
    protected function registerJanitorClasses()
    {
        $this->app->singleton('Janitor\Codebase', function ($app) {
            $codebase = new Codebase($app['path'], $app['config']->get('janitor::ignored'));
            $codebase->setCache($app['cache']);

            // Bind router if available
            if ($app->bound('router')) {
                $codebase->setRoutes($app['router']->getRoutes());
            }

            return $codebase;
        });
    }
}
