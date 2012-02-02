<?php

class Admin_IndexController extends Tp_Controller_Action
{

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
            $this->errorMessage('You have to login first!');
            $this->_redirect('/auth/login');
        }

		$uri = $this->getRequest()->getPathInfo();
		$activenav = $this->view->navigation()->findByUri($uri);
		$activenav->active = true;
		
    }

    public function indexAction()
    {
		
    }

    public function sitemapAction()
    {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
    	echo $this->view->navigation()->sitemap();
    }

}













