#!/bin/bash
echo "DROP DATABASE IF EXISTS traxpacking_test; CREATE DATABASE traxpacking_test" | mysql -u traxpacking --password=traxpacking
phpunit
