<?php

namespace kriskbx\mikado\Providers;

use DirectoryIterator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
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
        $this->assertConfigDir();

        $mikado = new Mikado();

        foreach (new DirectoryIterator($this->configPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $this->addManager($fileInfo, $mikado);
        }

        $this->app->singleton('kriskbx\mikado\Mikado', function ($app) use ($mikado) {
            return $mikado;
        });
    }

    /**
     * Add manager to mikado.
     *
     * @param DirectoryIterator $fileInfo
     * @param Mikado $mikado
     */
    protected function addManager($fileInfo, &$mikado)
    {
        $manager = new Manager();

        $pathParts = pathinfo($fileInfo->getBasename());
        $model = $pathParts['filename'];

        $this->addFormatter($manager, $model, 'MetaFormatter');
        $this->addFormatter($manager, $model, 'RemapFormatter');
        $this->addFormatter($manager, $model, 'FilterFormatter');

        $mikado->add($model, $manager);
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
        // Get the config
        $config = (include $this->configPath . '/' . $model . '.php');

        if(!isset($config[$formatter]))
            return;

        $formatterClass = 'kriskbx\mikado\Formatters\\' . $formatter;

        if (is_array($config[$formatter]) && count($config[$formatter]) > 0) {
            $manager->add(new $formatterClass($config[$formatter]));
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

    protected function assertConfigDir()
    {
        if (!file_exists($this->configPath))
            mkdir($this->configPath);
    }
}
