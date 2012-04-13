<?php


/**
 * MapDataProvider - sample class to expose via JSON-RPC
 */
class MapDataProvider
{

	/**
	 * This hangs for a given number of seconds and is for testing async
	 * calls to make sure it doesn't lock up the browser.
	 *
	 * @param int $sleepTime
	 * @return boolean
	 */
	public function hang($sleepTime)
	{
		sleep($sleepTime);
		return true;
	}

    public function random() {
        return rand(0, 199929);
    }

    public function hello() {
        return "Hello World";
    }

	/**
	 * This explodes the server (throws exception)
	 */
	public function explode() {
		throw new Exception('BOOM');
	}

	/* Takes an associative array (javascript object).  Returns true
	 * if its able to unpack.
	 *
	 * @param array
	 * @return boolean
	 */
	public function arrayTest(array $arr) {
		if(($arr["hi"] == 1) && ($arr["there"] == 2)) {
			return true;
		}

		return false;
	}
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../../',
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once '../../library/Zend/Application.php';
require_once '../../library/Zend/Config/Ini.php';

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV
    , $config
);
$application->bootstrap();


clearstatcache();


$server = new Zend_Json_Server();
$server->setClass('MapDataProvider');

if ('GET' == $_SERVER['REQUEST_METHOD']) {
	// Hang if we're asked to
	if(isset($_REQUEST['hang']) && $_REQUEST['hang']) {
		sleep((int)$_REQUEST['hang']);
	}

    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('rpc.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

    // Grab the SMD
    $smd = $server->getServiceMap();

    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}

try {
	$server->handle();
} catch(Exception $e) {
	$err = new Zend_Json_Server_Error($e->getMessage());
	echo $err;
}