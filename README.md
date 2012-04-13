## Traxpacking - Backpacking by GPS ##

Make the best out of your GPS-Supported travelings.

Note: It is in very early development phase. Feel free to reuse whatever is useful to you

Some brief overview of what it should do, once finished:
    - Publish Pictures and GPS Tracks on a Google Map
    - Have Slideshows that integrate with the map
    - Attach URIs (for Blog integration)


# Requirements
    sass:   $ gem install sass
    doctrineORM and PHPUnit:
        $ sudo pear channel-discover pear.symfony.com
        $ sudo pear channel-discover pear.phpunit.de
        $ sudo pear channel-discover pear.symfony-project.com
        $ pear channel-discover pear.doctrine-project.org
        $ sudo pear update-channels
        $ sudo pear install symfony2/Yaml
        $ sudo pear install symfony2/Console
        $ sudo pear install –alldeps doctrine/DoctrineORM
        $ sudo pear install –alldeps phpunit/Phpunit

# Setup process
	git clone git://github.com/uhon/traxpacking.git traxpacking

	cd traxpacking
    cp public/_htaccess public/_htaccess
    
	mkdir -p -m 777 data/session
	mkdir -p -m 777 public/cache
	mkdir -p -m 777 public/media
	chmod 777 public/compressed

	git submodule init
	git submodule update

	cd library
	svn co http://framework.zend.com/svn/framework/standard/branches/release-1.11/library/Zend
	svn co http://framework.zend.com/svn/framework/extras/branches/release-1.11/library/ZendX

	cd ../scripts
	php doctrine.php orm:schema-tool:create
	php doctrine.php orm:generate-proxies

