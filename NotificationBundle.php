<?php

namespace KungFu\NotificationBundle;

use KungFu\NotificationBundle\DependencyInjection\KungfuNotificationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NotificationBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new KungfuNotificationExtension();
    }
}
