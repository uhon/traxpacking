<?php
/**
 * User: uhon
 * Date: 2012/02/14
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Controller_Plugin_AclManager extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Default user role if user is not logged in or an invalid role is found in
	 * the database
	 * @var string Name indicating the role of the user (guest/member/admin)
	 */
	private $_defaultRole = 'guest';

	/**
	 * The action to dispatch if a user doesn't have sufficient privileges
	 * @var array Array containing the controller and action to dispatch
	 */
	private $_authController = array(
        'module' => 'default',
		'controller' => 'auth',
        'action' => 'login');

	public function __construct(Zend_Auth $auth) {
		$this->auth = $auth;
		$this->acl = new Zend_Acl();

		// add the different user roles
		$this->acl->addRole(new Zend_Acl_Role($this->_defaultRole));
		$this->acl->addRole(new Zend_Acl_Role('member'), $this->_defaultRole);
		$this->acl->addRole(new Zend_Acl_Role('admin'), array('member'));

        // default module
        $this->acl->addResource(new Zend_Acl_Resource('index'));
        $this->acl->addResource(new Zend_Acl_Resource('auth'));
        $this->acl->addResource(new Zend_Acl_Resource('error'));

		// admin module
        $this->acl->addResource(new Zend_Acl_Resource('admin'));
        $this->acl->addResource(new Zend_Acl_Resource('admin:index'), 'admin');
        $this->acl->addResource(new Zend_Acl_Resource('admin:poi'), 'admin');
		$this->acl->addResource(new Zend_Acl_Resource('admin:picture'), 'admin');

		// deny everything by default, explicitly allow access to resources and privileges
		$this->acl->deny()
            ->allow('guest', 'index')

            ->allow('guest', 'auth', 'login')
            ->deny('guest', 'auth', 'logout')
            ->deny('member', 'auth', 'login')
            ->allow('member', 'auth', 'logout')

            ->allow('admin', 'admin');
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		// get role of user
		$role = $this->getRole();

		// the ACL resource is the requested controller name
		$resource = $request->controller;

		if($request->module != 'default') {
			$resource = $request->module . ':' . $resource;

        }
        // the ACL privilege is the requested action name
		$privilege = $request->action;

		// if we haven't explicitly added the resource, check the default global
		// permissions
		if (!$this->acl->has($resource)) {
			$resource = null;
		}

		// access denied - reroute the request to the default action handler
		if (!$this->acl->isAllowed($role, $resource, $privilege)) {
			$request->setModuleName($this->_authController['module']);
			$request->setControllerName($this->_authController['controller']);
			$request->setActionName($this->_authController['action']);
		}
	}

	public function getAcl() {
		return $this->acl;
	}

	public function getRole() {
		if ($this->auth->hasIdentity()) {
			$role = $this->auth->getIdentity()->role;
		} else {
			$role = $this->_defaultRole;
		}

		if (!$this->acl->hasRole($role)) {
			$role = $this->_defaultRole;
		}

		return $role;
	}
}