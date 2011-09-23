<?php

class AuthController extends Zend_Controller_Action
{

    /**
     * @var Zend_Auth
     */
    protected $_auth = null;

    public function init()
    {
        $this->_loginForm = new Form_Login();
        $this->view->form = $this->_loginForm;
        
        // get auth service from bootstrap
        $bootstrap = $this->getInvokeArg('bootstrap');
        $this->_auth = $bootstrap->getResource('auth');
    }

    public function indexAction() {
        $this->_forward('login');
    }

	public function loginAction() {
		
		$form = new Form_Login();
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
			if($form->isValid($this->getRequest()->getPost())){
				$flashMessenger = $this->getHelper('FlashMessenger');
				$data = $form->getValues();
				$auth = Zend_Auth::getInstance();
				$authAdapter = new M\Auth\Adapter\Doctrine2(M\Reg::doctrine()->getEntityManager(), 'users', 'username', 'password');
				$authAdapter->setIdentityColumn('username');
				$authAdapter->setCredentialColumn('password');
				$authAdapter->setCredentialTreatment('SHA1(CONCAT(?, salt))');
				$authAdapter->setCredentialTreatment($data['password']);
				$authAdapter->setIdentity($data['username']);
				$result = $auth->authenticate($authAdapter);
				if($result->isValid()){
					$storage = new Zend_Auth_Storage_Session();
					$storage->write($authAdapter->getResultRowObject());
					$this->_redirect('/');
				} else {
					$flashMessenger->addMessage('Invalid username or password.');
					$this->view->errorMessage = "Invalid username or password. Please try again.";
					$this->_redirect('/auth/login');
				}
			}
		}
	}

	public function logoutAction() {
		$storage = new Zend_Auth_Storage_Session();
		$storage->clear();
		$this->_redirect('/auth/login');
	}

}
