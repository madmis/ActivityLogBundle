<?php

namespace ActivityLogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FormatterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ( ! $container->has('activity_log.formatter')) {
            return;
        }

        $definition = $container->findDefinition('activity_log.formatter');

        $formatters = $container->findTaggedServiceIds('activity_log.formatter');

        foreach ($formatters as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addFormatter',
                    array(
                        new Reference($id),
                        $attributes["entity"],
                    )
                );
            }
        }
    }
}
