<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 * Make use of Zend-Functionalities in a Static maner.
 */

class Tp_Shortcut
{
    const PIC_SMALL_X = 300;
    const PIC_SMALL_Y = 300;
    const PIC_MEDIUM_X = 900;
    const PIC_MEDIUM_Y = 600;
    /**
     * Get View
     * Does not work during bootstrap !
     * @return Zend_View
     */
    public static function getView()
    {
        $b = Zend_Controller_Front::getInstance()->getParam('bootstrap');

        if (null !== $b) {
            return $b->getResource('view');
        }

        return new Zend_View();
    }

    public static function debugMessage($message)
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $flashMessenger->setNamespace('debug');
        $flashMessenger->addMessage($message);
    }

    public static function infoMessage($message)
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $flashMessenger->setNamespace('info');
        $flashMessenger->addMessage($message);
    }

    public static function errorMessage($message)
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $flashMessenger->setNamespace('error');
        $flashMessenger->addMessage($message);
    }
}
