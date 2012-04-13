<?php

namespace Tp\Entity;
/**
 * User: uhon
 * Date: 2012/04/10
 * GitHub: git@github.com:uhon/traxpacking.git
 */

class UserTest extends \ModelTestCase
{
	/**
	 *
	 */
	public function testCanSaveUserAndRetrieveIt()
	{
		$user = new User();
        $user->username = "admin";
        $user->email = "admin@traxpacking.dev";
        $user->role = "admin";
        $user->password = "7dd12f3a9afa0282a575b8ef99dea2a0c1becb51";
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();

        $user = new User();
        $user->username = "member";
        $user->email = "member@traxpacking.dev";
        $user->role = "member";
        $user->password = "7dd12f3a9afa0282a575b8ef99dea2a0c1becb51";
        $em = $this->doctrineContainer->getEntityManager();
        $em->persist($user);
        $em->flush();
	}

}
