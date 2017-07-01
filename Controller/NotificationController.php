<?php

namespace KungFu\NotificationBundle\Controller;

use KungFu\NotificationBundle\Entity\NotificationSettingInterface;
use KungFu\NotificationBundle\Form\NotificationSettingsForm;
use KungFu\NotificationBundle\Service\NotificationSettingFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class NotificationController extends Controller
{
    /**
     * Displays a page that allows users to manage which notifications
     * they want to subscribe to.
     *
     * @Route("/notifications", name="notification_settings")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function settingsAction(Request $request)
    {
        if (!$this->isGranted('IS_FULLY_AUTHENTICATED')) {
            return $this->redirect('./');
        }

        if (($user = $this->getUser()) === null) {
            return $this->redirect('./');
        }

        /**
         * First we need to load all of our services and configurations from the container.
         *
         * @var Session $session
         * @var NotificationSettingFactoryInterface $settings
         * @var PropertyAccessorInterface $accessor
         * @var mixed[] $config
         */
        $session  = $this->get('session');
        $settings = $this->get('notification.settings.factory');
        $accessor = $this->get('property_accessor');
        $config   = $this->getParameter('notification.config');

        /**
         * Then we need to load all of our user specific properties and settings. These will be used to
         * make sure that we have the correct user notification settings.
         *
         * @var integer $userId
         * @var NotificationSettingInterface[] $user_settings
         */
        $userId        = $accessor->getValue($user, $config['user']['properties']['identifier']);
        $user_settings = $settings->getAllByUser($userId);

        /**
         * Now that we have the notifications from the configuration, and the users notification settings
         * we can build our form. The form takes all of the notifications from the configuration file
         * and creates checkboxes for them. Then it looks at the users settings to see if that notification
         * should be checked or not.
         */
        $form = $this->createForm(NotificationSettingsForm::class, null, array(
            'notifications' => $config['notifications'],
            'user_settings' => $user_settings,
        ));

        /**
         * When the form has been submitted by the user, we need to process it.
         */
        if ($form->handleRequest($request)->isSubmitted()) {
            if ($form->isValid()) {

                /**
                 * In order to make the system easier to use, we have forgone the need to maintain a data model for the
                 * notification setting form. If we did have a data model, that would require the developers to change
                 * both the configuration file and the data model every time they wanted to add a new notification.
                 *
                 * Instead we are storing our information in an array that we can rotate through and then send to the
                 * setting factory to be updated.
                 *
                 * @var boolean[] $data
                 */
                $data = $form->getData();
                foreach ($data as $key => $value) {
                    $settings->update($userId, $key, $value);
                }

                $session->getFlashBag()
                        ->add('success', 'Successfully saved the notification settings.');

            } else foreach ($form->getErrors() as $error) {
                $session->getFlashBag()
                        ->add('error', $error->getMessage());
            }
        }

        return $this->render('@Notification/settings.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
