<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
		//$uri = $this->getRequest()->getPathInfo();
		//$activenav = $this->view->navigation()->findByUri($uri);
		//$activenav->active = true;
		
    }

    public function indexAction()
    {
		
    }

    public function sitemapAction()
    {
    	//$this->_helper->layout()->disableLayout();
		//$this->_helper->viewRenderer->setNoRender(true);
    	//echo $this->view->navigation()->sitemap();
    }

}













