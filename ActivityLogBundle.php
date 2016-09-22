<?php

namespace ActivityLogBundle;

use ActivityLogBundle\DependencyInjection\Compiler\FormatterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ActivityLogBundle
 * @package ActivityLogBundle
 */
class ActivityLogBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormatterPass());
    }
}
