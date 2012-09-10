<?php
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
SQL Query Builder

Build SQL Queries dynamically with
a better-looking code rather than just
define the 'distracting' SQL string syntax

DETAILED EXAMPLE SCRIPT
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/

/* Including the class definition */
require_once("SqlQueryBuilder.class.php");

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/
/* INSERT */
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/

/* Creating an object for the INSERT query */
$query = new SqlQueryBuilder("insert");

/* Set the table we want to work with */
$query->setTable("users");

/**
 * Add a first column and value
 * Note: Remember to add the single quotes when inserting strings 
 */
$query->addColumn("email");
$query->addValue("'david@example.com'");
 /* some code... (could be a loop) */
$query->addColumn("name");
$query->addValue("'David R. Demaree'");

/**
 * We can add data adding all the columns first
 * and then their values, just be careful about the order 
 */
$query->addColumn("country");
$query->addColumn("phone_number");
 /* some code... */
$query->addValue("'Mexico'");
$query->addValue("5514891568");

print "Insert query: " . $query->buildQuery() . "\n";

/**
 * Output should be:
 * INSERT INTO users (email, name, country, phone_number) VALUES ('david@example.com', 'David R. Demaree', 'Mexico', 5514891568);
 */

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/
/* SELECT STATEMENT */
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/


 /* Creating an object for the SELECT query */
$query = new SqlQueryBuilder("select");

/* Set the table to work with */
$query->setTable("Users");

/**
  * Specify which columns to select
  * You can use $query-addColumn("*") instead
  */
$a_variable = "email"; // Column names can be variable rather than constant

$query->addColumn("name");
$query->addColumn($a_variable);

/* Set the WHERE clause */
$query->setWhere("id = 452");

/* setOrderBy(), setGroupBy(), setHaving() */ 
$query->setOrderBy("last_name ASC");

/* At last, once the data is complete, we build the SQL string */
$sql_query = $query->buildQuery();

print "Select query: " . $sql_query . "\n";

/* Output should be:
	SELECT name, email FROM Users WHERE id = 452 ORDER BY last_name ASC;
*/

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/
/* The same rules apply to UPDATE and DELETE
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/
/* ARBITRARY QUERY */
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*/
/**
 * Note:
 * Maybe you are thinking that it is much easier to directly
 * define a string containing the needed query than setting an object
 * just to copy a string. In fact it is! But this function is usefull
 * for manteining coding standards through your code
 */
$query = new SqlQueryBuilder("query");
$query->setQuery("DROP TABLE users");

print "Arbitrary query: " . $query->buildQuery() . "\n";

/* Output should be:
	DROP TABLE users;
*/
?>