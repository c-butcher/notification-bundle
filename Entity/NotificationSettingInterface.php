<?php

namespace KungFu\NotificationBundle\Entity;

interface NotificationSettingInterface
{
    public function getId();
    public function getUserId();
    public function setUserId($id);
    public function getKey();
    public function setKey($key);
    public function getEnabled();
    public function setEnabled($enabled);
}
