<?php

function ip2long6($ipv6)
{
    if (filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
        return false;

    $ip_n = inet_pton($ipv6);
	
	$ipv6long = implode('', array_map(function($bytes) use ($ip_n) {
		return sprintf('%08b', ord($ip_n[$bytes]));
	}, range(0, 15)));

    return gmp_strval(gmp_init($ipv6long, 2));
}

function long2ip6($ipv6long, $compress = true)
{
	$bin = sprintf('%0128s', gmp_strval($ipv6long, 2));

	$ipv6 = implode(':', array_map(function($bytes) use ($bin) {
		$bin_part = substr($bin, $bytes * 16, 16);
		
		return str_pad(dechex(bindec($bin_part)), 4, '0', STR_PAD_LEFT);
	}, range(0, 7)));

	return $compress ? inet_ntop(inet_pton($ipv6)) : $ipv6;
}

function not($number, $length)
{
	return gmp_xor($number, gmp_sub(gmp_pow(2, $length), 1));
}
