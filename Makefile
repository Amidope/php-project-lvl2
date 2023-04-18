install:
	composer install
dump:
	composer dump-autoload
validate:
	composer validate
gendiff:
	./bin/gendiff tests/fixtures/nestedFile1.yml tests/fixtures/nestedFile2.yml
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
lint-diff:
	composer exec --verbose phpcs -- --standard=PSR12 --report=diff src bin tests
lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

