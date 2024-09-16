<?php

namespace PouyaSoft\SDateBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class PouyaSoftSDateBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load an XML, PHP or YAML file
        $container->import('../config/services.yml');
    }
}
