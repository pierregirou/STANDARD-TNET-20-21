<?php
	$hostname="localhost";
	$username="jottpro";
	$password="6i64)2no";
	$database="jottpro";

	$conn = mysql_connect($hostname,$username,$password) or die (mysql_error());
	mysql_select_db($database,$conn) or die (mysql_error());
	mysql_query('SET NAMES UTF8');			
?>