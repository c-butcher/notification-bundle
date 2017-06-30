<?php

namespace KungFu\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="notification_settings")
 */
class NotificationSetting implements NotificationSettingInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $userId;

    /**
     * @ORM\Column(type="string", name="notification_key", length=75)
     */
    public $key;

    /**
     * @ORM\Column(type="integer")
     */
    public $schedule;

    /**
     * @ORM\Column(type="datetime")
     */
    public $lastSent;

    /**
     * @ORM\Column(type="boolean")
     */
    public $enabled;

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    public function getLastSent()
    {
        return $this->lastSent;
    }

    public function setLastSent($lastSent)
    {
        $this->lastSent = $lastSent;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
