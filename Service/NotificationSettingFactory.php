<?php

namespace KungFu\NotificationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use KungFu\NotificationBundle\Entity\NotificationSettingInterface;

/**
 * Class NotificationSettingFactory
 *
 * @package KungFu\NotificationBundle\Service
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class NotificationSettingFactory implements NotificationSettingFactoryInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repo;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * NotificationSettingFactory constructor.
     *
     * @param EntityManagerInterface $manager
     * @param array $config
     */
    public function __construct(EntityManagerInterface $manager, array $config)
    {
        $this->config  = $config;
        $this->class   = $config['settings']['class'];
        $this->manager = $manager;
        $this->repo    = $manager->getRepository($this->class);
    }

    /**
     * Returns all of the notification settings for a specific user.
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getAllByUser($userId)
    {
        return $this->repo->findBy(array(
            'userId' => $userId
        ));
    }

    /**
     * Returns a specific notification setting for a specific user.
     *
     * @param integer $userId
     * @param string $key
     *
     * @return null|object
     */
    public function getByUserKey($userId, $key)
    {
        return $this->repo->findOneBy(array(
            'userId' => $userId,
            'key'    => $key,
        ));
    }

    /**
     * Creates a users notification setting.
     *
     * @param integer $userId
     * @param string $key
     *
     * @return null|NotificationSettingInterface
     *
     * @throws \Exception
     */
    public function create($userId, $key)
    {
        /** @var NotificationSettingInterface $setting */
        if (($setting = $this->getByUserKey($userId, $key)) !== null) {
            return $setting;
        }

        $setting = new $this->class();

        if (!($setting instanceof NotificationSettingInterface)) {
            throw new \Exception("The notification settings class must implement the NotificationSettingInterface.");
        }

        if (!isset($this->config['notifications'][$key])) {
            throw new \Exception(sprintf("The notification '%s' does not exist in the configuration file.", $key));
        }

        $setting->setUserId($userId);
        $setting->setKey($key);
        $setting->setEnabled($this->config['notifications'][$key]['enabled']);

        $this->manager->persist($setting);
        $this->manager->flush();

        return $setting;
    }

    /**
     * Updates a users notification setting.
     *
     * @param int $userId
     * @param string $key
     * @param bool $enabled
     *
     * @return NotificationSettingInterface|null|object
     */
    public function update($userId, $key, $enabled)
    {
        if (($setting = $this->getByUserKey($userId, $key)) === null) {
            $setting = $this->create($userId, $key);
        }

        $setting->setEnabled($enabled);

        $this->manager->flush();

        return $setting;
    }
}
