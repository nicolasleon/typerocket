<?php
namespace TypeRocket\Plugin;

use \TypeRocket\Config as Config;
/*
|--------------------------------------------------------------------------
| Plugin Loader
|--------------------------------------------------------------------------
|
| Load plugins. All plugins should live in a dir and must include an
| init.php file to get loaded. There are no file only plugins folders
| must be used.
|
*/
class Loader
{
    public $plugins = null;

    function __construct(PluginCollection $plugins)
    {
        $this->setCollection($plugins);
    }

    function setCollection(PluginCollection $collection) {
        $this->plugins = apply_filters('tr_plugins_collection', $collection);
    }

    function load()
    {
        $plugins_list = $this->plugins;
	    $paths = Config::getPaths();

        foreach ($plugins_list as $plugin) {
            $folder = $paths['plugins'] . '/' . $plugin . '/';

            if (file_exists($folder . 'init.php')) {
                /** @noinspection PhpIncludeInspection */
                include $folder . 'init.php';
            }
        }
    }

}