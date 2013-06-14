<?php

class IPv4Network extends Network
{
	protected $length = 32;
	protected $sprintf_padder = "%032s";

	protected function setAddress($address)
	{
		$this->address = gmp_init(ip2long($address));
	}

	public function formatAddress($address)
	{
		return long2ip(gmp_strval($address));
	}

	protected function verifyAddress($address)
	{
		$flags = array('flags' => FILTER_FLAG_IPV4);
		
		return filter_var($address, FILTER_VALIDATE_IP, $flags);
	}
}
