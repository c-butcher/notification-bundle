<?php

namespace KungFu\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use KungFu\NotificationBundle\Entity\NotificationSetting;
use KungFu\NotificationBundle\Service\NotificationSettingFactory;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kungfu_notifications');

        $rootNode
            ->children()
                ->arrayNode('mailer')->isRequired()
                    ->children()
                        ->arrayNode('from')
                            ->children()
                                ->scalarNode('name')->isRequired()->end()
                                ->scalarNode('address')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('reply_to')
                            ->children()
                                ->scalarNode('name')->isRequired()->end()
                                ->scalarNode('address')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('user')->isRequired()
                    ->children()
                        ->scalarNode('class')->isRequired()->end()
                        ->arrayNode('properties')
                            ->children()
                                ->scalarNode('identifier')->defaultValue('id')->end()
                                ->scalarNode('email')->defaultValue('email')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('settings')
                    ->children()
                        ->scalarNode('class')->defaultValue(NotificationSetting::class)->end()
                        ->scalarNode('factory')->defaultValue(NotificationSettingFactory::class)->end()
                    ->end()
                ->end()
                ->arrayNode('scheduling')
                    ->prototype('integer')->end()
                ->end()
                ->arrayNode('notifications')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->isRequired()->end()
                            ->scalarNode('description')->isRequired()->end()
                            ->scalarNode('template')->isRequired()->end()
                            ->integerNode('schedule')->defaultValue(0)->end()
                            ->booleanNode('enabled')->defaultValue(true)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
