<?php

class IndexController extends Tp_Controller_Action
{

    public function init()
    {
		$uri = $this->getRequest()->getPathInfo();
		$activenav = $this->view->navigation()->findByUri($uri);
		$activenav->active = true;
		
    }

    public function indexAction()
    {
		/*$this->infoMessage("i'm info");
		$this->infoMessage("i'm second info");
        $this->errorMessage("i'm error");
		$this->errorMessage("i'm second error");*/
    }

    public function sitemapAction()
    {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
    	echo $this->view->navigation()->sitemap();
    }

    public function countriesAction()
    {

    }

    public function blogAction()
    {

    }

}













