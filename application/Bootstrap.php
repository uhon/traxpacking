<?php

use Bisna\Application\Resource\Doctrine;
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initAuth()
	{
		$this->bootstrap('session');
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$view = $this->getResource('view');
			$view->user = $auth->getIdentity();
		}
		return $auth;
	}

    protected function _initAccessControl()
	{
		$front = Zend_Controller_Front::getInstance();
		$this->_aclManager = new Tp_Controller_Plugin_AclManager(Zend_Auth::getInstance());
		$front->registerPlugin($this->_aclManager);
	}

	protected function _initNavigation()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');

		$container = new Zend_Navigation($config);
		$view->navigation($container)
			->setAcl($this->_aclManager->getAcl())
			->setRole($this->_aclManager->getRole());
	}

	protected function _initFlashMessenger()
	{
		/** @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		if ($flashMessenger->hasMessages()) {
			$view = $this->getResource('view');
			$view->messages = $flashMessenger->getMessages();
		}
	}
	
	public function _initAutoloader()
	{
		if (!class_exists('Doctrine\Common\ClassLoader')) {
			require_once APPLICATION_PATH . '/../library/doctrine2-common/lib/Doctrine/Common/ClassLoader.php';
		}
		$autoloader = \Zend_Loader_Autoloader::getInstance();

		$foo = new \Doctrine\Common\ClassLoader('Doctrine\Common');
		$autoloader->pushAutoloader(array($foo, 'loadClass'), 'doctrine2-common\lib\Doctrine\Common');
		$foo->register();
		
		$foo = new \Doctrine\Common\ClassLoader('Doctrine\ORM');
		$autoloader->pushAutoloader(array($foo, 'loadClass'), 'doctrine2-orm\lib\Doctrine\ORM');
		$foo->register();
		
		$foo = new \Doctrine\Common\ClassLoader('Doctrine\DBAL');
		$autoloader->pushAutoloader(array($foo, 'loadClass'), 'doctrine2-dbal\lib\Doctrine\DBAL');
		$foo->register();
		
		$foo = new \Doctrine\Common\ClassLoader('Tp');
		$autoloader->pushAutoloader(array($foo, 'loadClass'), 'Tp');
		$foo->register();
		
		$bisnaAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
		$autoloader->pushAutoloader(array($bisnaAutoloader, 'loadClass'), 'Bisna');
		$bisnaAutoloader->register();
		
		
		$mAutoloader = new \Doctrine\Common\ClassLoader('M');
		$autoloader->pushAutoloader(array($mAutoloader, 'loadClass'), 'M');
		$mAutoloader->register();
		
		//$classLoader = new \Doctrine\Common\ClassLoader('Gedmo');
		//$autoloader->pushAutoloader(array($classLoader, 'loadClass'), 'Gedmo');
		//$classLoader->register();
		
		//$evm = new \Doctrine\Common\EventManager();
		//$treeListener = new \Gedmo\Tree\TreeListener();
		//$evm->addEventSubscriber($treeListener);
	}


    protected function _initViewHelpers()
   	{
   		$this->bootstrap('layout');
   		$layout = $this->getResource('layout');
   		$view = $layout->getView();
   		$view->doctype('XHTML1_TRANSITIONAL');
   		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
   	}
}
