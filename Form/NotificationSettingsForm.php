<?php

namespace KungFu\NotificationBundle\Form;

use KungFu\NotificationBundle\Entity\NotificationSettingInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationSettingsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['notifications'] as $key => $config) {

            /**
             * We need to look through the users notification settings, and find out whether this
             * notification is enabled or not. If it is enabled, then we can make sure it is checked.
             * If the user does not have this notification saved, then we will use the notifications
             * default value.
             *
             * @var NotificationSettingInterface $notification_setting */
            $enabled = $config['enabled'];
            foreach ($options['user_settings'] as $notification_setting) {
                if ($notification_setting->getKey() == $key) {
                    $enabled = $notification_setting->getEnabled();
                    break;
                }
            }

            $builder->add($key, CheckboxType::class, array(
                'label'    => $config['description'],
                'data'     => $enabled,
                'value'    => 1,
                'required' => false,
            ));
        }

        $builder->add('submit', SubmitType::class, array(
            'label' => 'Save'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'notifications' => array(),
            'user_settings' => array(),
        ));
    }
}