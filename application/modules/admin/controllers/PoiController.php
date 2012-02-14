<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */

use \Tp\Entity\Pictures;
use \Tp\Entity\Pois;

class Admin_PoiController extends Tp_Controller_Action
{

    public function indexAction() {
        $repository = $this->_em->getRepository('Tp\Entity\Pois');
        $pois = $repository->findAll();
        $this->view->pois = array();
        foreach($pois as $poi) {
            /* @var \Tp\Entity\Pois $poi */
            $this->view->pois[] = array(
                $poi->title,
                $poi->latitude,
                $poi->longitude,
                $poi->description,
                $this->view->linkiconEdit($poi->getEditUrl()),
                $this->view->linkiconDelete($poi->getDeleteUrl())
            );
        }
    }

    public function editAction() {
        $add = true;
        $poi = $this->_em->find('Tp\Entity\Pois', $this->_getParam('poi', 0));
        $picture = new \Tp\Entity\Pictures();
        if($poi) {
            $add = false;
            if($poi->picture) {
                $picture = $poi->picture;
            }
        } else {
            $poi = new Pois();
        }

        $form = new Form_Poi(
            array(
                'picture' => ($picture->filename == null) ? null : $picture,
                'cachedPicture' => $this->_getParam('cachedPicture'),
                'removablePicture' => $this->_getParam('removablePicture'),
                'action' => $this->view->url(array(
                    'module' => 'admin',
                    'controller' => 'poi',
                    'action' => 'edit',
                    'pic' => $this->_getParam('pic'),
                    'poi' => $this->_getParam('poi'))
                    , null, true)
            )
        );

        if($this->_request->isPost()) {
            if($form->isValid($this->_request->getPost())) {
                $cachedPicture = $form->getValue('cachedPicture');

                if($cachedPicture != null) {
                    $picVersions = glob(APPLICATION_PATH . '/../public/cache/' . $cachedPicture . "*");
                    foreach($picVersions as $pv) {
                        if(rename($pv, APPLICATION_PATH . '/../public/media/' . basename($pv)) === false) {
                            throw new Exception("failed to move $pv to media directory (is directory writable?)\n");
                        }
                    }
                    $removablePicture = APPLICATION_PATH . '/../public/upload/' . $form->getValue('removablePicture');
                    if(file_exists($removablePicture)) {
                        unlink($removablePicture);
                    }

                    $picture->filename = $cachedPicture;
                    $picture->datetime = new \DateTime($form->getValue('dateTime'));
                    $this->_em->persist($picture);
                } elseif($picture->filename !== null) {
                    $picture->datetime = new \DateTime($form->getValue('dateTime'));
                }

                $poi->title = $form->getValue('title');
                $poi->description = $form->getValue('description');
                $poi->latitude = $form->getValue('latitude');
                $poi->longitude = $form->getValue('longitude');
                $poi->picture = $picture;
                $poi->type = 1;
                $this->_em->persist($poi);

                $this->_em->flush();

                $message = $add ? 'POI was successfully added' : 'POI was successfully updated';
                $this->infoMessage($message);
                $this->_redirect("/admin/poi");

            }
        } else {
            if($poi && !$add) {
                $form->populate($poi->toArray());
            }
        }

        $this->view->form = $form;
    }
}
