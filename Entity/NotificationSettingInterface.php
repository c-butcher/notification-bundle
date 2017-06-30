<?php

namespace KungFu\NotificationBundle\Entity;

interface NotificationSettingInterface
{
    const SCHEDULE_IMMEDIATELY = 0;
    const SCHEDULE_DAILY = 86400;
    const SCHEDULE_WEEKLY = 604800;
    const SCHEDULE_MONTHLY = 2628000;

    public function getId();
    public function getUserId();
    public function setUserId($id);
    public function getKey();
    public function setKey($key);
    public function getSchedule();
    public function setSchedule($schedule);
    public function getLastSent();
    public function setLastSent($lastSent);
    public function getEnabled();
    public function setEnabled($enabled);
}
