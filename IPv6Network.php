<?php

class IPv6Network extends Network
{
	protected $length = 128;
	protected $sprintf_padder = "%0128s";
	
	protected function setAddress($address)
	{
		$this->address = gmp_init(ip2long6($address));
	}

	public function formatAddress($address)
	{
		return long2ip6($address);
	}

	protected function verifyAddress($address)
	{
		$flags = array('flags' => FILTER_FLAG_IPV6);
		
		return filter_var($address, FILTER_VALIDATE_IP, $flags);
	}
}
