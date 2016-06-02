<?php

namespace ActivityLogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package ActivityLogBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('activity_log');

        $rootNode
            ->children()
                ->scalarNode('formatter_prefix')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
