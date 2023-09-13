<?php


namespace Tb07\DouKuaiOpenApi\Kernel\Core;

class BaseService
{
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }
}
