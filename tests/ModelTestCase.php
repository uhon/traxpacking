<?php


/**
 * Description of ModelTestCase
 * 
 * 
 */
class ModelTestCase
	extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @var \Bisna\Application\Container\DoctrineContainer
	 */
	protected $doctrineContainer;

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		$this->backupGlobals = false;
		$this->backupStaticAttributes = false;
		parent::__construct($name, $data, $dataName);
	}
	
	public static function dropSchema($params) {
		if (file_exists($params['dbname'])) {
			unlink($params['dbname']);
		}
	}

	public function getClassMetas($path, $namespace) {
		$metas = array();
		if ($handle = opendir($path)) {
			while(false !== ($file = readdir($handle))) {
				if(strstr($file,'.php')) {
					list($class) = explode('.',$file);
					$metas[] = $this->doctrineContainer
									->getEntityManager()->getClassMetadata($namespace . $class);
				}
			}
		}
		return $metas;
	}


	public function setUp() {
		global $application;
		Zend_Session::$_unitTestEnabled = true;
		$application->bootstrap();
		$this->doctrineContainer = Zend_Registry::get('doctrine');

		$tool = new \Doctrine\ORM\Tools\SchemaTool($this->doctrineContainer->getEntityManager());
        $tool->dropDatabase();
		$metas = $this->getClassMetas(APPLICATION_PATH . '/../library/Tp/Entity','Tp\Entity\\');
		
		$tool->createSchema($metas);
		
		parent::setUp();
	}

	public function tearDown() {
		$this->doctrineContainer = Zend_Registry::get('doctrine');
		self::dropSchema($this->doctrineContainer->getConnection()->getParams());
		parent::tearDown();
	}
}
