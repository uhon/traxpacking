<?php
/**
 * User: uhon
 * Date: 02.02.12
 * GitHub: git@github.com:uhon/traxpacking.git
 */

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Track
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property float $latitudeMin
 * @property float $latitudeMax
 * @property float $longitudeMin
 * @property float $longitudeMax
 * @property TrackCategory $category
 * @property Gps $gps
 * @property Polyline $polylines
 * @property Route $routes
 * @property \Doctrine\Common\DateTime\DateTime $createddate
 * @property \Doctrine\Common\DateTime\DateTime $modifieddate
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Track
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
	 * @var string $title
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $title;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(type="string")
	 */
	private $description;

    /**
     * @var float $latitudeMin
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $latitudeMin;

    /**
     * @var float $latitudeMax
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $latitudeMax;

    /**
     * @var float longitudeMin
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $longitudeMin;

    /**
     * @var float longitudeMax
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $longitudeMax;

    /**
	 * @var TrackCategory $category
	 *
	 * @ORM\ManyToOne(targetEntity="TrackCategory")
	 */
	private $category;

    /**
     * @var Gps $gps
     *
     * @ORM\OneToMany(targetEntity="Gps", mappedBy="track")
     */
	private $gps;

    /**
     * @var Polyline $polylines
     *
     * @ORM\OneToMany(targetEntity="Polyline", mappedBy="track")
     */
	private $polylines;

    /**
     * @var Route $routes
     *
     * @ORM\ManyToMany(targetEntity="Route", mappedBy="tracks")
     */
	private $routes;

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
    
    public function getEditUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'poi',
                'action' => 'edit',
                'poi' => $this->id
            ), null, true
        );
    }

    public function getDeleteUrl() {
        return \Tp_Shortcut::getView()->url(
            array(
                'module' => 'admin',
                'controller' => 'poi',
                'action' => 'delete',
                'poi' => $this->id
            ), null, true
        );
    }
}
