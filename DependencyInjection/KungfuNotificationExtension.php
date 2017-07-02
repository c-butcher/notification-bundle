<?php

namespace KungFu\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class KungfuNotificationExtension
 *
 * @package KungFu\NotificationBundle\DependencyInjection
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class KungfuNotificationExtension extends Extension
{
    /**
     * In order for our bundle to operate correctly, we need to load our configuration, services and
     * any other information that is required by our bundle.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter("notification.config", $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * This allows us to change the name that Symfony uses to identify our bundle.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'kungfu_notifications';
    }
}
