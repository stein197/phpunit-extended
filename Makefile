.PHONY: *
.SILENT:

exec = docker run --tty --workdir "/app" --volume "./:/app" --user=$(shell id -u):$(shell id -g) --dns=8.8.8.8 --dns=8.8.4.4 composer sh -c

install: # Install the project
	$(exec) "composer install"

check: test phpstan # Run PHPUnit and PHPStan

test: # Run PHPUnit tests
	$(exec) "composer test"

phpstan: # Run PHPStan checks
	$(exec) "composer phpstan"
