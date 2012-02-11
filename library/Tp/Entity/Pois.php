<?php
/**
 * User: uhon
 * Date: 02.02.12
 * GitHub: git@github.com:uhon/traxpacking.git
 */

namespace Tp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pois
 * 
 * @property int $poiId
 * @property float $latitude
 * @property float $longitude
 * @property string $title
 * @property string $description
 * @property int $type
 * @property int $picture
 * @property string $createddate
 * @property string $modifieddate
 *
 * @ORM\Table(name="pois")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Pois
{
	/**
	 * @var integer $poiId
	 *
	 * @ORM\Column(name="poi_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $poiId;

	/**
	 * @var float $latitude
	 *
	 * @ORM\Column(name="latitude", type="float", nullable=false)
	 */
	private $latitude;

	/**
	 * @var string $longitude
	 *
	 * @ORM\Column(name="longitude", type="float", nullable=false)
	 */
	private $longitude;

	/**
	 * @var integer $title
	 *
	 * @ORM\Column(name="title", type="string", nullable=false)
	 */
	private $title;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(name="description", type="string")
	 */
	private $description;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="integer")
     */
	private $type;

    /**
     * @var string $picture
     *
     * @ORM\Column(name="picture", type="integer")
     * @ORM\ManyToOne(targetEntity="Pictures")
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     */
	private $picture;


	/**
	 * @var datetime $createddate
	 *
	 * @ORM\Column(name="createddate", type="datetime", nullable=false)
	 */
	private $createddate;

	/**
	 * @var datetime $modifieddate
	 *
	 * @ORM\Column(name="modifieddate", type="datetime", nullable=false)
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
}
