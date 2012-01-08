<?php

class AuthController extends Zend_Controller_Action
{

    /**
     * @var Zend_Auth
     */
    protected $_auth = null;
    private $_loginForm = null;

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
				$authAdapter = new Tp_Auth_Adapter_Doctrine2(Zend_Registry::get('doctrine')->getEntityManager(), 'users', 'email', 'password');

                $authAdapter->setIdentity($_POST['email'])
                                    ->setCredential(hash('ripemd160', $_POST['password']))
                                    ->setIdentityField('name')
                                    ->setCredentialField('password');

				$result = $auth->authenticate($authAdapter);
				if($result->isValid()){
					$storage = new Zend_Auth_Storage_Session();
					$storage->write($authAdapter->getResultRowObject());
                    $flashMessenger->addMessage('Invalid email or password.');
					$this->_redirect('/');
				} else {
					$flashMessenger->addMessage('Invalid email or password.');
					$this->view->errorMessage = "Invalid email or password. Please try again.";
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
