<?php

class Tp_Provider_FlashMessage {
    public function provideMessages() {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $flashMessenger->setNamespace('default');
        $messageString = $this->getMessages($flashMessenger, 'default');
        $flashMessenger->setNamespace('info');
        $messageString .= $this->getMessages($flashMessenger, 'info');
        $flashMessenger->setNamespace('error');
        $messageString .= $this->getMessages($flashMessenger, 'error');
        return $messageString;
    }

    private function getMessages(Zend_Controller_Action_Helper_FlashMessenger $flashMessenger, $namespace) {
        $message = '';
        $visability = '';
        if($flashMessenger->hasMessages($flashMessenger) || $flashMessenger->hasCurrentMessages() ) {
            $message = $this->getMessageContent($flashMessenger);
        } else {
            $visability = 'style="display:none;"';
        }
        $flashMessenger->clearMessages();

        return '<div id="' . $namespace . 'Messages" class="flashMessages" ' . $visability . '>' . $message . '</div>';
    }



    private function getMessageContent(Zend_Controller_Action_Helper_FlashMessenger $flashMessenger) {
        $msgString = "";
        if($flashMessenger->hasCurrentMessages()) {
            $msgString .= $this->formatMessages($flashMessenger->getCurrentMessages());
            $flashMessenger->clearCurrentMessages();
        }

        if($flashMessenger->hasMessages()) {
           $msgString .= $this->formatMessages($flashMessenger->getMessages());
        }
        return $msgString;
    }

    private function formatMessages($messages) {
       $returnString = "";
        $returnString .= '<ul class="msgItems">';
        foreach ($messages as $message) {
            $returnString .= '<li class="msgItem">' . $message . '</li>';
        }
        $returnString .= '</ul>';
        return $returnString;
    }


}