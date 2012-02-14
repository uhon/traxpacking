<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 * Make use of Zend-Functionalities in a Static maner.
 */

class Tp_Shortcut
{
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
}
