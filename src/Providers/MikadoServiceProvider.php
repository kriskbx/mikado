<?php

namespace kriskbx\mikado\Providers;

use DirectoryIterator;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use kriskbx\mikado\Formatters\MetaFormatter;
use kriskbx\mikado\Manager;

class MikadoServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/mikado/model.php' => config_path('mikado/model.php'),
        ]);
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        foreach (new DirectoryIterator(config_path('mikado')) as $fileInfo) {
            if ($fileInfo->isDot())
                continue;

            $this->app->singleton('mikado.' . $fileInfo->getBasename(), function ($app) use ($fileInfo) {
                $manager = new Manager();
                $model = $fileInfo->getBasename();

                $this->addFormatter($manager, $model, 'MetaFormatter');
                $this->addFormatter($manager, $model, 'RemapFormatter');
                $this->addFormatter($manager, $model, 'FilterFormatter');
                $this->addFormatter($manager, $model, 'RequestFormatter');

                return $manager;
            });
        }
    }

    /**
     * Add formatter to manager.
     *
     * @param Manager $manager
     * @param string $model
     * @param string $formatter
     */
    protected function addFormatter(&$manager, $model, $formatter)
    {
        $config = config("mikado.$model.$formatter");

        if (is_array($config) && count($config) > 0)
            $manager->add(new $formatter($config));
    }
}
