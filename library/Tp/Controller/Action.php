<?php

class Tp_Controller_Action extends Zend_Controller_Action
{
    private $_isXHR = false;

    /* @var $_em \Doctrine\ORM\EntityManager */
    protected  $_em = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        $this->_em = Zend_Registry::get('doctrine')->getEntityManager();
        if(APPLICATION_ENV === 'development') {
            $this->_em->getConnection()->getConfiguration()->setSQLLogger(
                new Tp_Logging_DebugSQLLogger()
            );
        }


        if($request->isXmlHttpRequest()) {
            $this->_isXHR = true;
            Zend_Controller_Action_HelperBroker::getExistingHelper('layout')->disableLayout();
        }

        parent::__construct($request, $response, $invokeArgs);
    }

    public function debugMessage($message)
    {
        Tp_Shortcut::debugMessage($message);
    }

    public function infoMessage($message)
    {
        Tp_Shortcut::infoMessage($message);
    }

    public function errorMessage($message)
    {
        Tp_Shortcut::errorMessage($message);
    }


    public function postDispatch() {
        if($this->_isXHR) {
            $msgProvider = new Tp_Provider_FlashMessage();
            $this->getResponse()->appendBody($msgProvider->provideMessages());

        }
    }
}