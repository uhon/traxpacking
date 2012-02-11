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
 * @property int $pictureId
 * @property string $filename
 * @property string $datetime
 * @property string $createddate
 * @property string $modifieddate
 *
 * @ORM\Table(name="pictures")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Pictures
{
	/**
	 * @var integer $pictureId
	 *
	 * @ORM\Column(name="picture_id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $pictureId;

	/**
	 * @var string $filename
	 *
	 * @ORM\Column(name="filename", type="string", length=255, nullable=false)
	 */
	private $filename;

	/**
	 * @var datetime $datetime
	 *
	 * @ORM\Column(name="datetime", type="datetime")
	 */
	private $description;

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

}
