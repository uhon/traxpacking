<?php

class Tp_Controller_Action extends Zend_Controller_Action
{
    private $_flashMessenger = null;
    private $_isXHR = false;

    /* @var $_em \Doctrine\ORM\EntityManager */
    protected  $_em = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $this->_em = Zend_Registry::get('doctrine')->getEntityManager();
        ///$this->_em->getConfiguration()->setSQLLogger(new Doctrine\DBAL\Logging\EchoSQLLogger());

        if($request->isXmlHttpRequest()) {
            $this->_isXHR = true;
            Zend_Controller_Action_HelperBroker::getExistingHelper('layout')->disableLayout();
        }

        parent::__construct($request, $response, $invokeArgs);
    }


    public function infoMessage($message)
    {
        $this->_flashMessenger->setNamespace('info');
        $this->_flashMessenger->addMessage($message);
    }

    public function errorMessage($message)
    {
        $this->_flashMessenger->setNamespace('error');
        $this->_flashMessenger->addMessage($message);
    }


    public function postDispatch() {
        if($this->_isXHR) {
            $msgProvider = new Tp_Provider_FlashMessage();
            $this->getResponse()->appendBody($msgProvider->provideMessages());

        }
    }
}