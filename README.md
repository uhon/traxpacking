Zend Framework with Doctrine2 packaged for rapid deployment
--------------

Want to quickly start a Doctrine2 / Zend project without having to worry about the internals?
	
	git clone git://github.com/MontmereLimited/Zend1Doctrine2RapidDeploy.git myproject
	cd myproject
	chmod 777 data/session
	git submodule init 
	git submodule update
	cd library
	svn co http://framework.zend.com/svn/framework/standard/branches/release-1.11/library/Zend
	svn co http://framework.zend.com/svn/framework/extras/branches/release-1.11/library/ZendX
	cd ../scripts
	php doctrine.php orm:schema-tool:create
	php doctrine.php orm:generate-proxies

Done. You even have a database setup (sqlite by default, you can easily change it to mysql in application.ini, just read the comments), and an example Doctrine Entity to start with.

Eventually, I plan to do make a way to register users, login, and have some basic ACL stuff setup, because that's a pretty common first-requirement.
