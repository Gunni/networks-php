<?php

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
