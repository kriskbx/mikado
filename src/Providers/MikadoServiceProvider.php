<?php

namespace kriskbx\mikado\Providers;

use DirectoryIterator;
use Illuminate\Support\ServiceProvider;
use kriskbx\mikado\Formatters\MetaFormatter;
use kriskbx\mikado\Manager;
use kriskbx\mikado\Mikado;

class MikadoServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $configPath;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/mikado/model.php' => $this->configPath . '/model.php',
        ]);
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->setConfigPath();

        $mikado = new Mikado();

        foreach (new DirectoryIterator($this->configPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $manager = new Manager();
            $pathParts = pathinfo($fileInfo->getBasename());
            $model = $pathParts['filename'];

            $this->addFormatter($manager, $model, 'MetaFormatter');
            $this->addFormatter($manager, $model, 'RemapFormatter');
            $this->addFormatter($manager, $model, 'FilterFormatter');
            $this->addFormatter($manager, $model, 'RequestFormatter');

            $mikado->add($model, $manager);
        }

        $this->app->singleton('kriskbx\mikado\Mikado', function ($app) use ($mikado) {
            return $mikado;
        });
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

        if (is_array($config) && count($config) > 0) {
            $manager->add(new $formatter($config));
        }
    }

    /**
     * Lumen throws an error on the current version when calling config_path.
     * This is a quick work-around to use the package with Lumen and Laravel.
     */
    protected function setConfigPath()
    {
        if (!function_exists('config_path')) {
            $this->configPath = \base_path() . '/config/mikado';
        } else {
            $this->configPath = \config_path('mikado');
        }
    }
}
