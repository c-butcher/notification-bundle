<?php

namespace KungFu\NotificationBundle\Service;

use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

interface NotifierInterface
{
    public function __construct(Swift_Mailer $mailer, EngineInterface $template, PropertyAccessorInterface $accessor, NotificationSettingFactoryInterface $settings, array $userProperties);
    public function send($users, $key, array $params = array());
}
