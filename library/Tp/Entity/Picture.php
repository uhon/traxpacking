<?php
/**
 * User: uhon
 * Date: 02.02.12
 * GitHub: git@github.com:uhon/traxpacking.git
 */

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Picture
 * 
 * @property int $id
 * @property string $filename
 * @property string $description
 * @property PictureCategory $category
 * @property \Doctrine\Common\DateTime\DateTime $datetime
 * @property Poi $poi
 * @property \Doctrine\Common\DateTime\DateTime $createddate
 * @property \Doctrine\Common\DateTime\DateTime $modifieddate
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Picture
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string $filename
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $filename;

    /**
   	 * @var string $description
   	 *
   	 * @ORM\Column(type="string", nullable=true)
   	 */
   	private $description;


    /**
	 * @var PictureCategory $category
	 *
	 * @ORM\ManyToOne(targetEntity="PictureCategory")
	 */
	private $category;

	/**
	 * @var \Doctrine\Common\DateTime\DateTime $datetime
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $datetime;

    /**
     * @var Poi $poi
     *
     * @ORM\ManyToOne(targetEntity="Poi")
     */
    private $poi;

	/**
	 * @var \Doctrine\Common\DateTime\DateTime $createddate
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $createddate;

	/**
	 * @var \Doctrine\Common\DateTime\DateTime $modifieddate
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	private $modifieddate;
	
	/**
	 * @ORM\PrePersist
	 */
	public function datePrePersist() {
		$this->createddate = new \DateTime('now');
		$this->modifieddate = $this->createddate;
	}
	
	/**
	 * @ORM\PreUpdate 
	 */
	public function datePreUpdate () {
		$this->modifieddate = new \DateTime('now');
	}

    /**
    	 * @param string
    	 * @param mixed
    	 * @return self
    	 */
    	public function __set($property, $value)  {
    		if(method_exists($this, 'set' . ucfirst($property))) {
    			return call_user_func(array($this, 'set' . ucfirst($property)), $value);
    		}
    		else {
    			$this->$property = $value;
    		}
    		return $this;
    	}

    	/**
    	 *
    	 * @param string
    	 * @return mixed
    	 */
    	public function __get($property) {
    		if(method_exists($this, 'get' . ucfirst($property))) {
    			return call_user_func(array($this, 'get' . ucfirst($property)));
    		}
    		else {
    			return $this->$property;
    		}
    	}

    /**
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

    public function getDeleteUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'picture',
                'action' => 'delete',
                'picture' => $this->id
            ), null, true
        );
    }

    public function getAssignUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'picture',
                'action' => 'edit',
                'picture' => $this->id
            ), null, true
        );
    }
}
