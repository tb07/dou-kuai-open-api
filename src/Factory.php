<?php


namespace Tb07\DouKuaiOpenApi;

/**
 * Class Factory.
 *
 * @method static \Tb07\DouKuaiOpenApi\JinRiTeMai\Application            jinRiTeMai(array $config)
 */
class Factory
{
    /**
     * @param string $name
     * @param array $config
     *
     * @return \Tb07\DouKuaiOpenApi\Kernel\Core\ServiceContainer
     */
    public static function make($name, array $config)
    {
        $namespace   = ucwords($name);
        $application = "\\DouKuaiOpenApi\\{$namespace}\\Application";

        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
