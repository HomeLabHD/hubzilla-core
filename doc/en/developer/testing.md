# Testing

Hubzilla uses [PHPUnit] for automated testing, often called _unit testing_ or _integration testing_. 

The tests are written as PHP classes, and live under the `tests/unit` subdirectory of the main Hubzilla repository. This guide will explain how to run the tests, how the tests are structured, and how you can write your own tests.

## Running the test suite

### Installing the dependencies

To be able to run the tests you have to install the developer dependencies listen in the `composer.json` file. Make sure you have [composer installed] on your development system, and then run:

    % composer install

This will install phpunit and a few other dependencies. It will also update some files in the `vendor/composer` subdirectory. This may seem a bit noisy, but it's important that these changes are *not* committed to the repository!

**Warning:** Do not commit the changes to the files in the `vendor/composer` directory to your repository!

### Setting up the test database

We have included a script (`tests/create_test_db.sh`) that will help you set up the test database. You can run it like this:

    % HZ_TEST_DB_TYPE=mysql ./tests/create_test_db.sh

If you are running PostgreSQL instead, you create the test db like this:

    % HZ_TEST_DB_TYPE=postgres ./tests/create_test_db.sh

The script make some assumptions about your setup, but everything is configurable using environment variables. If you need any customization, see the script source for the details.

### Running the tests

Once you have installed the developer dependencies and set up the test database, you can run the tests like this:

    % ./vendor/bin/phpunit -c tests/phpunit.xml

Again, by default the configuration in `tests/phpunit.xml` makes some assumptions about your setup, which can be overridden using environment variables. See `tests/phpunit.xml` for the details.

You can also run a specific test, or a specific set of tests using the `--filter` argument to PHPUnit:

    % ./vendor/bin/phpunit -c tests/phpunit.xml --filter Autoname

Will run any test mathcing "Autoname".

### Generating coverage reports

To generate coverage reports you need a driver that is able to generate the coverage info that PHPUnit will collect. We recommend [Xdebug], but see the PHPUnit [page on code coverage analysis](https://docs.phpunit.de/en/9.6/code-coverage-analysis.html) for alternatives and details.

With Xdebug properly set up, you can generate the coverage report like this:

    % XDEBUG_MODE=coverage ./vendor/bin/phpunit -c ./tests/phpunit.xml

This will generate a number of HTML files in directories under the `tests/results/coverage/` subdirectory.

Open the `index.php  file in your web browser to view the stats.

### Debugging

With Xdebug installed, you can also do step debugging and a number of other things to debug and get information about the execution of the tests. See the [Xdebug] pages and your prefered editor for how to set this up.

## Test structure and organization

Tests are located in the `tests/unit` subdirectory, and subdirectories under there again, more or less reflecting the directory layout of the core code repository.

Tests are written as PHP classes that extends the UnitTestCase class (located in `tests/unit/UnitTestcase.php`). The file name and the test class it contains should be the same, and they _must_ end with `Test.php`.

Examples are:

* `tests/unit/includes/AccountTest.php`
* `tests/unit/Lib/ActivityTest.php`

The test classes contain one or more test functions, these must be public and prefixed with `test`.

Here's an example:

    use Zotlabs\Tests\Unit\UnitTestCase;
    
    class AccountTest extends UnitTestCase {
        public function test_get_account_by_id_returns_existing_account() {
            $account = get_account_by_id(42);
            $this->assertNotFalse($account);
            $this->assertEquals(
                $this->fixtures['account'][0]['account_email'], 
                $account['account_email']
            );
        }

Notice that we try to make the name of the test funtion as descriptive as possible.

The class can also contain any number of other functions to keep things tidy and nice. These can be private, protected or public as needed by the test code.

Results and artifacts from the test runs will be left in the `tests/results/` directory. This will typically include the test log, code coverage report etc.

### Hubzilla specific features

#### Test database access:

Ideally it should be able to test each part of the code in isolation, where any dependencies should be replaced by stubs, mocks or fakes.

This is not feasible with a legacy codebase like Hubzilla, that has not been written with testability in mind. For this reason we use a separate test database, and make it available to the code under test using the same mechanisms as Hubzilla normally do.

This means that any code that executes database queries will behave as normal when called by a test.

To make this work we connect to the database before running each test function. We also load some initial data into the database to make sure things work as expected, and that we have a known state of the database when the test runs.

When the test finishes, the test database is wiped clean, so that we have the same consistent state when the next test runs.

All of this is handled by the `UnitTestCase` base class.

#### Database fixtures:

We need some predictable content in the database to set up things like logging, and other content that's useful in general for the tests. These are database fixtures that are loaded into the database for each test run.

The database fixtures are located in the `tests/unit/include/dba/_files` directory, and consist of YAML files, where each file represents the content of a database table with the same name as the file itself.

While database fixtures are nice to have, we try to keep the number of them as minimal as possible. It's usually better to add any content needed for a specific test in the test itself.

#### Fakes:

Fakes are classes that we can pass to the code under test that will look and (to the code under test) behave the same as the original class. These are useful when we're able to pass objects to the code under test, as we can control it completely from the test code.

Fakes are located under the `tests/fakes` directory.

## Writing your own tests

To be done

[PHPUnit]: https://phpunit.de
[composer installed]: https://getcomposer.org/
[Xdebug]: https://xdebug.org/