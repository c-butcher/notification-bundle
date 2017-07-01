<?php

namespace KungFu\NotificationBundle\Service;

use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Notifier
 *
 * @package KungFu\NotificationBundle\Service
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class Notifier implements NotifierInterface
{
    /**
     * Mailing service for sending emails.
     *
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * Template engine which allows us to render templates.
     *
     * @var EngineInterface
     */
    protected $template;

    /**
     * Service that allows us to access properties on an object.
     *
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * Factory for loading and saving the users notification settings.
     *
     * @var NotificationSettingFactoryInterface
     */
    protected $settings;

    /**
     * Configuration values of the notification bundle.
     *
     * @var array
     */
    protected $config;

    /**
     * Notifier constructor.
     *
     * @param Swift_Mailer $mailer
     * @param EngineInterface $template
     * @param PropertyAccessorInterface $accessor
     * @param NotificationSettingFactoryInterface $settings
     * @param array $config
     */
    public function __construct(Swift_Mailer $mailer, EngineInterface $template, PropertyAccessorInterface $accessor, NotificationSettingFactoryInterface $settings, array $config)
    {
        $this->mailer   = $mailer;
        $this->template = $template;
        $this->accessor = $accessor;
        $this->settings = $settings;
        $this->config   = $config;
    }

    /**
     * Returns the users unique identifier.
     *
     * @param object|array $user
     *
     * @return integer
     */
    protected function getUserId($user)
    {
        return $this->accessor->getValue($user, $this->config['user']['properties']['identifier']);
    }

    /**
     * Returns the users email address.
     *
     * @param object|array $user
     *
     * @return string
     */
    protected function getUserEmail($user)
    {
        return $this->accessor->getValue($user, $this->config['user']['properties']['email']);
    }

    /**
     * Sends an email notification to the end-user.
     *
     * @param object[]|array[] $users
     * @param string $key
     * @param array $params
     */
    public function send($users, $key, array $params = array())
    {
        if (!isset($this->config['notifications']) || !isset($this->config['notifications'][$key])) {
            throw new Exception(sprintf("Unable to locate the '%s' notification within the notification configuration.", $key));
        }

        if (!isset($this->config['user']) || !isset($this->config['user']['properties'])) {
            throw new Exception("The user properties must be set within the notification configuration.");
        }

        $from    = $this->config['from'];
        $replyTo = $this->config['reply_to'];
        $subject = $this->config['notifications'][$key]['subject'];

        $notification = new Swift_Message();
        $notification->setFrom($from['address'], $from['name']);
        $notification->setReplyTo($replyTo['address'], $replyTo['name']);
        $notification->setSubject($subject);
        $notification->setContentType('text/html');

        foreach ($users as $user) {
            $userId  = $this->getUserId($user);
            $address = $this->getUserEmail($user);

            /**
             * Before we can send the email, we need to load the users notification setting and see if they
             * actually want to receive the notification. When the user doesn't have the notification setting,
             * it will create one for them using the default values in the configuration. */
            if (($setting = $this->settings->getByUserKey($userId, $key)) === null) {
                $setting = $this->settings->create($userId, $key);
            }

            if (!$setting->getEnabled()) {
                continue;
            }

            /**
             * We are rendering the email for each user, this allows us to send the user information into the email
             * template so that the notification can be customized specifically to the user. eg(Great New Chris!). */
            $content = $this->template->render(
                $this->config['notifications'][$key]['template'],
                array_merge($params, array('recipient' => $user))
            );

            $notification->setTo($address);
            $notification->setBody($content);
            $this->mailer->send($notification);
        }
    }
}
