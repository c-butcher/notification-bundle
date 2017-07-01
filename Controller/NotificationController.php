<?php

namespace KungFu\NotificationBundle\Controller;

use KungFu\NotificationBundle\Form\NotificationSettingsForm;
use KungFu\NotificationBundle\Service\NotificationSettingFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class NotificationController extends Controller
{
    /**
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
         * @var NotificationSettingFactoryInterface $settings
         * @var PropertyAccessorInterface $accessor
         * @var mixed[] $config
         */
        $settings      = $this->get('notification.settings.factory');
        $accessor      = $this->get('property_accessor');
        $config        = $this->getParameter('notification.config');
        $userId        = $accessor->getValue($user, $config['user']['properties']['identifier']);
        $user_settings = $settings->getAllByUser($userId);

        $form = $this->createForm(NotificationSettingsForm::class, null, array(
            'notifications' => $config['notifications'],
            'user_settings' => $user_settings,
        ));

        if ($form->handleRequest($request)->isSubmitted()) {
            if ($form->isValid()) {

                $data = $form->getData();
                foreach ($data as $key => $value) {
                    if (($setting = $settings->getByUserKey($userId, $key)) === null) {
                        $setting = $settings->create($userId, $key);
                    }

                    $setting->setEnabled($value);
                }

                $this->getDoctrine()->getManager()->flush();

            } else foreach ($form->getErrors() as $error) {

            }
        }

        return $this->render('@Notification/settings.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}