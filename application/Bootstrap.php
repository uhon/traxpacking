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

}
