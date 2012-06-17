<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */

use \Tp\Entity\Picture as Picture;

class Admin_PictureController extends Tp_Controller_Action
{
    public function indexAction()
    {
        $pictures = glob(APPLICATION_PATH . "/../public/upload/*.jpg");
        $pictures = array_merge($pictures, glob(APPLICATION_PATH . "/../public/upload/*.JPG"));
        $this->view->newPictures = array();
        foreach($pictures as $p) {
            $exif=exif_read_data($p);
            if($exif === false) {
                throw new Exception('exif-Data for picture ' . basename($p) . ' could not be read! please solve the Problem manually (exiftool)!');
            }

            $this->view->newPictures[] = array(
                'name' => basename($p),
                'size' => round(filesize($p) / 1048576, 2) . 'MB',
                'datetime' => $exif['DateTimeOriginal'],
                'initializeLink' => '<a href="' . $this->view->url(array('action' => 'convert', 'pic' => basename($p))) . '">initialize</a>'
            );
        }
        $this->view->unassignedPictures = array();
        $unassigned = $this->_em->getRepository('Tp\Entity\Picture')->findBy(array('poi' => null), array('datetime' => 'ASC'));
        foreach($unassigned as $p) {
            /* @var \Picture $p */
            $this->view->unassignedPictures[] = array(
                'name' => $p->filename,
                'datetime' => $p->datetime->format('Y-m-d H:i:s'),
                'editLink' => $this->view->linkiconEdit($p->getAssignUrl(), 'assign to poi'),
            );
        }
    }

    public function editAction() {
        $picture = $this->_em->find('Tp\Entity\Picture', $this->_getParam('picture', 0));

        if($picture) {

            $form = new Form_Picture(
                array(
                    'id' => 'pictureForm',
                    'picture' => $picture,
                    'action' => $this->view->url(array(
                        'module' => 'admin',
                        'controller' => 'picture',
                        'action' => 'edit',
                        'picture' => $picture->id)
                        , null, true
                    )
                )
            );

            if($this->_request->isPost()) {
                if($form->isValid($this->_request->getPost())) {
                    $picture->description = $form->getValue('description');
                    $picture->poi = $this->_em->find('Tp\Entity\Poi', $form->getValue('poi'));
                    $picture->datetime = new \DateTime($form->getValue('datetime'));

                    $this->_em->persist($picture);

                    $this->_em->flush();

                    $message = 'Picture was successfully updated';
                    $this->infoMessage($message);
                    $form->redirectOnSuccess();

                }
            } else {
                if($picture) {
                    $data = $picture->toArray();
                    $data["datetime"] = $data["datetime"]->format('Y-m-d H:i:s');
                    if($data["poi"] === null) {
                        unset($data["poi"]);
                    }
                    $form->populate($data);
                }
            }

            $this->view->form = $form;
        }
    }

    public function convertAction() {
        if(($pic = $this->_getParam('pic')) !== null) {
            $uploadedPicture = APPLICATION_PATH . "/../public/upload/" . $pic;
            $exif=exif_read_data($uploadedPicture);

            $genericFilename = APPLICATION_PATH . "/../public/media/" . uniqid('tp_');


            if(rename($uploadedPicture, $genericFilename . "_orig.jpg") === false) {
                throw new Exception("failed to move $uploadedPicture to media directory (is directory writable?)\n");
            }

            require_once APPLICATION_PATH . '/../library/phpThumb/src/ThumbLib.inc.php';

            $thumb = PhpThumbFactory::create($genericFilename . "_orig.jpg");
            $this->rotateThumbIfNeeded($thumb, $exif);
            $thumb->resize(Tp_Shortcut::PIC_MEDIUM_X, Tp_Shortcut::PIC_MEDIUM_Y);
            $thumb->save($genericFilename . '_medium.jpg');

            $thumb = PhpThumbFactory::create($genericFilename . "_orig.jpg");
            $this->rotateThumbIfNeeded($thumb, $exif);
            $thumb->resize(Tp_Shortcut::PIC_SMALL_X, Tp_Shortcut::PIC_SMALL_Y);
            $thumb->save($genericFilename . '_small.jpg');

            $dateTimeOfPicture = new Zend_Date($exif['DateTimeOriginal'], "yyyy:MM:dd HH:mm:ss");

            $picture = new Picture();
            $picture->filename = basename($genericFilename);
            $picture->datetime = new \DateTime($dateTimeOfPicture->get('yyyy-MM-dd HH:mm:ss'));

            $this->_em->persist($picture);
            $this->_em->flush();

            $this->infoMessage("Picture ${$uploadedPicture} initialized");
            $this->_redirect('/admin/picture/index');
        }
    }

    private function rotateThumbIfNeeded(GdThumb $thumb, array $exif)
    {
        if(isset($exif['Orientation'])) {

            switch($exif['Orientation']) {
                case 1: // nothing
                    break;
                case 2: // horizontal flip
                    break;
                case 3: // 180 rotate
                    $thumb->rotateImage();
                    $thumb->rotateImage();
                    break;
                case 4: // vertical flip
                    break;
                case 5: // vertical flip + 90 rotate right
                    $thumb->rotateImage();
                    break;
                case 6: // 90 rotate right
                    $thumb->rotateImage();
                    break;

                case 7: // horizontal flip + 90 rotate right
                    break;
                case 8:    // 90 rotate left
                    $thumb->rotateImage('CC');
                    break;
            }
        }
    }
}
