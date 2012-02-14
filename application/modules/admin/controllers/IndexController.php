<?php

class Admin_IndexController extends Tp_Controller_Action
{
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













