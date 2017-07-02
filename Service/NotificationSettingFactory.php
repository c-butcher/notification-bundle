<?php

namespace KungFu\NotificationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use KungFu\NotificationBundle\Entity\NotificationSettingInterface;

class NotificationSettingFactory implements NotificationSettingFactoryInterface
{
    protected $class;

    protected $config;

    protected $repo;

    protected $manager;

    public function __construct(EntityManagerInterface $manager, array $config)
    {
        $this->config  = $config;
        $this->class   = $config['settings']['class'];
        $this->manager = $manager;
        $this->repo    = $manager->getRepository($this->class);
    }

    public function getAllByUser($userId)
    {
        return $this->repo->findBy(array(
            'userId' => $userId
        ));
    }

    public function getByUserKey($userId, $key)
    {
        return $this->repo->findOneBy(array(
            'userId' => $userId,
            'key'    => $key,
        ));
    }

    public function create($userId, $key)
    {
        if (($setting = $this->getByUserKey($userId, $key)) === null) {
            $setting = new $this->class();
        }

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

    public function update($userId, $key, $enabled)
    {
        if (($setting = $this->getByUserKey($userId, $key)) === null) {
            $setting = $this->create($userId, $key);
        }

        $setting->setEnabled($enabled);

        $this->manager->flush();
    }
}
