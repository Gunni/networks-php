<?php

function __autoload($class_name)
{
    require_once $class_name . '.php';
}

function parseCIDR($cidr)
{
	$pieces = explode('/', $cidr);
	
	return new IPv4Network($pieces[0], $pieces[1]);
}

$ranges = file_get_contents('http://rix.is/is-net.txt');

foreach (explode("\n", $ranges) as $value)
{
	if ($value == "")
		continue;

	$network = parseCIDR($value);
	
	$min = gmp_add($network->networkAddress(), 1);   //network + 1
	$max = gmp_sub($network->broadcastAddress(), 1); //broadcast - 1
	
	$count = gmp_sub($max, $min);
	
	echo $network->formatAddress(gmp_strval($min)) . " - ";
	echo $network->formatAddress(gmp_strval($max)) . "<br>\n";
	
	for ($i = 0; $i <= gmp_strval($count); $i++)
	{
		echo $network->formatAddress(gmp_add($min, $i)) . "<br>\n";
	}
}
