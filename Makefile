install:
	composer install
dump:
	composer dump-autoload
validate:
	composer validate
gendiff:
	./bin/gendiff
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

