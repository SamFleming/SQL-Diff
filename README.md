SQL Diff
========
PHP Library for comparing SQL files.

Please note this is in its VERY early stages of development.

Example use
-----------
In this example situation we have a MySQL dump of our developement (dev.sql) database and are wanting to compare it to the live (live.sql) database so we can perform a set of Alter commands to "sync" them.

```php
use \SqlDiff as S;
use \SqlDiff\Datasources as SD;

try
{
	$from = new SD\File("dev.sql");
	$to = new SD\File("live.sql");

	$sqldiff = new S\SqlDiff($from, $to);
}
catch(\SqlDiff\Exception $e)
{
	echo "<strong>SQL Diff Error:</strong> ".$e->getMessage();
}
```