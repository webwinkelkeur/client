all : vendor

test :
	vendor/bin/phpunit tests

vendor : composer.json composer.lock
	composer install
	touch vendor
