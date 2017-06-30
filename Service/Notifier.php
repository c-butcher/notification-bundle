<?php

namespace KungFu\NotificationBundle\Service;

use KungFu\NotificationBundle\Entity\NotificationSettingInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class Notifier implements NotifierInterface
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var EngineInterface
     */
    protected $template;

    /**
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * @var NotificationSettingFactoryInterface
     */
    protected $settings;

    /**
     * @var array
     */
    protected $config;

    public function __construct(Swift_Mailer $mailer, EngineInterface $template, PropertyAccessorInterface $accessor, NotificationSettingFactoryInterface $settings, array $config)
    {
        $this->mailer   = $mailer;
        $this->template = $template;
        $this->accessor = $accessor;
        $this->settings = $settings;
        $this->config   = $config;
    }

    protected function getUserId($user)
    {
        return $this->accessor->getValue($user, $this->config['user']['properties']['identifier']);
    }

    protected function getUserEmail($user)
    {
        return $this->accessor->getValue($user, $this->config['user']['properties']['email']);
    }

    public function send($users, $key, array $params = array())
    {
        if (!isset($this->config['notifications']) || !isset($this->config['notifications'][$key])) {
            throw new Exception(sprintf("Unable to locate the '%s' notification within the notification configuration.", $key));
        }

        if (!isset($this->config['user']) || !isset($this->config['user']['properties'])) {
            throw new Exception("The user properties must be set within the notification configuration.");
        }

        $notification = new Swift_Message();

        $subject = $this->config['notifications'][$key]['subject'];
        $content = $this->template->render(
            $this->config['notifications'][$key]['template'],
            $params
        );

        $notification->setSubject($subject);
        $notification->setBody($content);
        $notification->setContentType('text/html');

        $notification->setFrom(
            $this->config['mailer']['from']['address'],
            $this->config['mailer']['from']['name']
        );

        $notification->setReplyTo(
            $this->config['mailer']['reply_to']['address'],
            $this->config['mailer']['reply_to']['name']
        );

        foreach ($users as $user) {
            $userId    = $this->getUserId($user);
            $userEmail = $this->getUserEmail($user);

            if (($setting = $this->settings->getByUserKey($userId, $key)) === null) {
                $setting = $this->settings->create($userId, $key);
            }

            if (!$setting->getEnabled()) {
                continue;
            }

            $notification->setTo($userEmail);
            $this->mailer->send($notification);
        }
    }
}
