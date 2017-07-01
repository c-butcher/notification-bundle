<?php

namespace KungFu\NotificationBundle\Form;

use KungFu\NotificationBundle\Entity\NotificationSettingInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NotificationSettingsForm
 *
 * @package KungFu\NotificationBundle\Form
 * @author Chris Butcher <c.butcher@hotmail.com>
 */
class NotificationSettingsForm extends AbstractType
{
    /**
     * Build the notification settings form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * This form is being generated dynamically using the notifications found in the Symfony configuration
         * under kungfu_notifications. Each notification will be assigned a checkbox, which will let the user
         * decide whether to enable or disable the notification. */
        foreach ($options['notifications'] as $key => $config) {

            /**
             * This is in case the user hasn't saved this notification setting before. When the user doesn't
             * have a notification setting, we want to use the default value for that setting, which is provided
             * within the configuration file. */
            $enabled = $config['enabled'];

            /**
             * When a user already has the setting, then we need to make sure that our checkboxes are reflecting
             * whether the user has enabled the notification or not. */
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

    /**
     * We are setting default options which will be supplied to the form builder in the event that someone
     * forgets to send them in when compiling the form.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'notifications' => array(),
            'user_settings' => array(),
        ));
    }
}