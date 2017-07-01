<?php

namespace KungFu\NotificationBundle;

use KungFu\NotificationBundle\DependencyInjection\KungfuNotificationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NotificationBundle
 *
 * @package KungFu\NotificationBundle
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class NotificationBundle extends Bundle
{
    /**
     * In order to get our bundle to be recognized as 'kungfu_notifications' by Symfony, we needed
     * to change the extension that is loaded by the container.
     *
     * @return KungfuNotificationExtension
     */
    public function getContainerExtension()
    {
        return new KungfuNotificationExtension();
    }
}
