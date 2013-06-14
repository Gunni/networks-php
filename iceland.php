<?php

/**
 * Copyright (c) 2013 Gunnar Guðvarðarson, Gabríel Arthúr Pétursson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

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
