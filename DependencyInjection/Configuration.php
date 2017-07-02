<?php

namespace KungFu\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use KungFu\NotificationBundle\Entity\NotificationSetting;
use KungFu\NotificationBundle\Service\NotificationSettingFactory;

/**
 * Class Configuration
 *
 * @package KungFu\NotificationBundle\DependencyInjection
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * This method defines our bundles configuration layout within the Symfony configuration file.
     *
     * @return TreeBuilder
     */
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
                ->arrayNode('user')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue(NotificationSetting::class)->end()
                        ->scalarNode('factory')->defaultValue(NotificationSettingFactory::class)->end()
                    ->end()
                ->end()
                ->arrayNode('notifications')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
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
