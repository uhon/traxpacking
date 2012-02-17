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
        $message = "";
        if($flashMessenger->hasMessages($flashMessenger) || $flashMessenger->hasCurrentMessages() ) {
            $message .= '<div id="' . $namespace . 'Messages" class="flashMessages">';
            $message .= $this->getMessageContent($flashMessenger);
            $message .= '</div>';
            $flashMessenger->clearMessages();
        }
        return $message;
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
        foreach ($messages as $message) {
            $returnString .= '<ul class="msgItems">';
            $returnString .= '<li class="msgItem">' . $message . '</li>';
            $returnString .= '</ul>';
        }
        return $returnString;
    }


}