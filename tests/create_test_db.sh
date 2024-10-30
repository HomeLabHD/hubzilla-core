#!/usr/bin/env bash

#
# Copyright (c) 2016 Hubzilla
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
#

# Exit if anything fails
set -e

#
# Initialize some defaults if they're not set by the environment
#
: ${HZ_TEST_DB_TYPE:=postgres}

case $HZ_TEST_DB_TYPE in
	postgres | pg | pgsql )
		db_type="postgres"
		default_charset="UTF8"
		root_user="postgres"
		root_passwd=""
		;;

	mariadb | mysql )
		db_type="mysql"
		db_binary=$HZ_TEST_DB_TYPE
		default_charset="utf8mb4"
		root_user="root"
		root_passwd="root"
		;;

	* )
		echo "Unknown database type: '${HZ_TEST_DB_TYPE}'"
		exit
		;;
esac

: ${HZ_TEST_DB_ROOT_USER:=$root_user}
: ${HZ_TEST_DB_ROOT_PASS:=$root_passwd}
: ${HZ_TEST_DB_USER:=test_user}
: ${HZ_TEST_DB_PASS:=hubzilla}
: ${HZ_TEST_DB_NAME:=hubzilla_test_db}
: ${HZ_TEST_DB_CHARSET:=$default_charset}

echo "Creating Hubzilla test db..."

if [[ $db_type == "postgres" ]]
then
	psql --version
	psql -U $HZ_TEST_DB_ROOT_USER -c "SELECT VERSION();"

	psql -U $HZ_TEST_DB_ROOT_USER -v ON_ERROR_STOP=1 <<-EOSQL
		DROP DATABASE IF EXISTS $HZ_TEST_DB_NAME;
		DROP USER IF EXISTS $HZ_TEST_DB_USER;

		CREATE USER $HZ_TEST_DB_USER WITH PASSWORD '$HZ_TEST_DB_PASS';
		CREATE DATABASE $HZ_TEST_DB_NAME
			WITH
				OWNER $HZ_TEST_DB_USER
				ENCODING $HZ_TEST_DB_CHARSET;

	EOSQL

	export PGPASSWORD=$HZ_TEST_DB_PASS

	# Import table structure
	echo "Importing schema..."
	psql -U $HZ_TEST_DB_USER -v ON_ERROR_STOP=1 $HZ_TEST_DB_NAME < ./install/schema_postgres.sql

	# Show databases and tables
	psql -U $HZ_TEST_DB_USER -l
	psql -U $HZ_TEST_DB_USER -d $HZ_TEST_DB_NAME -c "\dt;"
else
	echo -e "\n--------------"
	echo "Client version:"
	echo -e "--------------\n"
	$db_binary --version

	$db_binary -v -u $HZ_TEST_DB_ROOT_USER -p$HZ_TEST_DB_ROOT_PASS -Ns -e "SELECT VERSION();"

	$db_binary -u $HZ_TEST_DB_ROOT_USER -p$HZ_TEST_DB_ROOT_PASS <<-EOSQL
		DROP DATABASE IF EXISTS $HZ_TEST_DB_NAME;
		CREATE DATABASE $HZ_TEST_DB_NAME CHARACTER SET $HZ_TEST_DB_CHARSET;

		DROP USER IF EXISTS $HZ_TEST_DB_USER;
		CREATE USER $HZ_TEST_DB_USER IDENTIFIED BY '$HZ_TEST_DB_PASS';

		GRANT ALL ON ${HZ_TEST_DB_NAME}.* TO $HZ_TEST_DB_USER;
	EOSQL

	echo -e "\n--------------"
	echo "Importing schema..."
	echo -e "--------------\n"
	$db_binary -u $HZ_TEST_DB_USER -p$HZ_TEST_DB_PASS $HZ_TEST_DB_NAME < ./install/schema_mysql.sql
	$db_binary -v -u $HZ_TEST_DB_ROOT_USER -p$HZ_TEST_DB_ROOT_PASS -Ns -e "show databases"
	$db_binary -v -u $HZ_TEST_DB_USER -p$HZ_TEST_DB_PASS $HZ_TEST_DB_NAME -Ns -e "show tables"
fi
