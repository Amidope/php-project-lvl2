install:
	composer install
dump:
	composer dump-autoload
validate:
	composer validate
gendiff:
	./bin/gendiff tests/fixtures/nestedFile1.json tests/fixtures/nestedFile2.json 
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

