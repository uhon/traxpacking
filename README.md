## Traxpacking - Backpacking by GPS ##

Make the best out of your GPS-Supported travelings.

Note: It is in very early development phase. Feel free to reuse whatever is useful to you

Some brief overview of what it should do, once finished:
    - Publish Pictures and GPS Tracks on a Google Map
    - Have Slideshows that integrate with the map
    - Attach URIs (for Blog integration)


# Setup process
	git clone git://github.com/uhon/traxpacking.git traxpacking

	cd traxpacking
	chmod 777 data/session
	chmod 777 public/cache
	chmod 777 public/media

	git submodule init
	git submodule update

	cd library
	svn co http://framework.zend.com/svn/framework/standard/branches/release-1.11/library/Zend
	svn co http://framework.zend.com/svn/framework/extras/branches/release-1.11/library/ZendX

	cd ../scripts
	php doctrine.php orm:schema-tool:create
	php doctrine.php orm:generate-proxies
