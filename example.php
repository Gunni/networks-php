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

echo "USAGE: /?ip=&lt;ipv4 address&gt;&mask=&lt;ipv4 network length&gt;&sizes=&lt;subnet sizes optional&gt;&ip6=&lt;ipv6 address&mask6=&lt;ipv6 network length&gt;&sizes6=&lt;ipv6 subnet sizes&gt;<br>\n";

// IPv4
if (isset($_GET['ip']))
{
	$address = isset($_GET['ip'])   ? $_GET['ip']   : '172.16.0.0';
	$mask    = isset($_GET['mask']) ? $_GET['mask'] : 22;

	echo str_pad(' IPv4 ', 200, "-", STR_PAD_BOTH)."<br>\n";

	$ip = new IPv4Network($address, $mask);

	if (isset($_GET['sizes']))
	{
		$subnets = explode(',', $_GET['sizes']);

		foreach ($subnets as $subnet)
		{
			$ip->addSubnet($subnet);
		}
	}

	echo $ip->summary();
}

// IPv6
if (isset($_GET['ip6']))
{
	echo str_pad(' IPv6 ', 200, "-", STR_PAD_BOTH)."<br>\n";

	$address6 = isset($_GET['ip6'])   ? $_GET['ip6']   : 'fe00::';
	$mask6    = isset($_GET['mask6']) ? $_GET['mask6'] : 64;

	$ip6 = new IPv6Network($address6, $mask6);

	if (isset($_GET['sizes6']))
	{
		$subnets = explode(',', $_GET['sizes6']);

		foreach ($subnets as $subnet)
		{
			$ip6->addSubnet($subnet);
		}
	}

	echo $ip6->summary();
}
