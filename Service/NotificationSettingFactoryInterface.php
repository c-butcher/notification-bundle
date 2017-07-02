<?php

namespace KungFu\NotificationBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use KungFu\NotificationBundle\Entity\NotificationSettingInterface;

interface NotificationSettingFactoryInterface
{
    public function __construct(EntityManagerInterface $manager, array $config);

    /**
     * @param $userId
     * @param $key
     * @return NotificationSettingInterface
     */
    public function getByUserKey($userId, $key);

    /**
     * @param $userId
     *
     * @return NotificationSettingInterface[]
     */
    public function getAllByUser($userId);

    /**
     * @param integer $userId
     * @param string  $key
     *
     * @return NotificationSettingInterface
     */
    public function create($userId, $key);

    /**
     * @param integer $userId
     * @param string  $key
     * @param boolean $enabled
     *
     * @return void
     */
    public function update($userId, $key, $enabled);
}
