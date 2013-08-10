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

abstract class Network
{
	protected $address;
	protected $bitmask;
	protected $length;
	protected $sprintf_padder;
	
	protected $last_network;
	protected $subnets = array();

	public function __construct($address, $bitmask)
	{
		if ($bitmask > $this->length - 2)
			throw new OutOfRangeException("Bitmask must be 2 bits less than the bitlength of the address type");
		
		if ($bitmask <= 1)
			throw new OutOfRangeException("Bitmask must be at least 1 bit");

		if ( ! $this->verifyAddress($address))
			throw new InvalidArgumentException();

		$this->setAddress($address);
		$this->setBitmask($bitmask);
		
		$this->last_network = $this->getNetworkAddress();
	}

	protected abstract function setAddress($address);

	public abstract function formatAddress($address);

	protected abstract function verifyAddress($address);

	protected function setBitmask($bitmask)
	{
		$this->bitmask = gmp_xor(gmp_sub(gmp_pow(2, $this->length), 1), gmp_sub(gmp_pow(2, $this->length - $bitmask), 1));
	}

	public function addSubnet($size)
	{
		$bits_needed = $this->length - ceil(log($size + 2, 2));
		
		$class = get_called_class();
		
		$new_network = new $class($this->last_network, $bits_needed);
		
		$this->subnets[]    = $new_network;
		$this->last_network = $new_network->getNextNetwork();
	}
	
	public function address()
	{
		return $this->address;
	}
	
	public function networkAddress()
	{
		return gmp_and($this->address, $this->bitmask);
	}

	public function lowestAddress()
	{
		return gmp_add($this->networkAddress(), 1);
	}

	public function highestAddress()
	{
		return gmp_sub($this->broadcastAddress(), 1);
	}

	public function broadcastAddress()
	{
		return gmp_or($this->networkAddress(), not($this->bitmask, $this->length));
	}

	public function hostCount()
	{
		return gmp_sub(gmp_sub($this->broadcastAddress(), $this->networkAddress()), 1);
	}
	
	// Public getters
	
	public function getAddress()
	{
		return $this->formatAddress($this->address);
	}
	
	public function getNetworkAddress()
	{
		return $this->formatAddress($this->networkAddress());
	}
	
	public function getLowestAddress()
	{
		return $this->formatAddress($this->lowestAddress());
	}
	
	public function getHighestAddress()
	{
		return $this->formatAddress($this->highestAddress());
	}
	
	public function getBroadcastAddress()
	{
		return $this->formatAddress($this->broadcastAddress());
	}
	
	public function getBitmask()
	{
		return $this->formatAddress($this->bitmask);
	}
	
	public function getBitCount()
	{
		$count = 0;
		
		foreach (str_split(gmp_strval($this->bitmask, 2)) as $bit)
		{
			if ($bit == "1")
			{
				$count++;
			}
		}
		
		return $count;
	}
	
	public function getHostCount()
	{
		return gmp_strval($this->hostCount());
	}
	
	public function getNextNetwork()
	{
		return $this->formatAddress(gmp_add($this->broadcastAddress(), 1));
	}

	// //
	
	protected function displayAddressBinary($address)
	{
		return sprintf("%0{$this->length}s", gmp_strval($address, 2));
	}

	public function summary()
	{
		$ret = "";
		
		$ret .= "<table border='1'>\n";
		$ret .= "\t<tr>\n";
		$ret .= "\t\t<th>Network Type</th>\n";
		$ret .= "\t\t<th>Provided address</th>\n";
		$ret .= "\t\t<th>Network address</th>\n";
		$ret .= "\t\t<th>First address</th>\n";
		$ret .= "\t\t<th>Last address</th>\n";
		$ret .= "\t\t<th>Broadcast address</th>\n";
		$ret .= "\t\t<th>Subnet mask</th>\n";
		$ret .= "\t\t<th>Subnet bitlength</th>\n";
		$ret .= "\t\t<th>Number of hosts</th>\n";
		$ret .= "\t</tr>\n";
		$ret .= "\t<tr>\n";
		$ret .= sprintf("\t\t<td>%s</td>\n", get_called_class());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getNetworkAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getLowestAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getHighestAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBroadcastAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBitmask());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBitCount());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getHostCount());
		$ret .= "\t</tr>\n";
		$ret .= "\t<tr>\n";
		
		if (count($this->subnets) > 0)
		{
			$ret .= "\t<th colspan='9'>SUBNETS</th>\n";

			foreach ($this->subnets as $subnet)
			{
				$ret .= $subnet->summary_subnet();
			}
		}
		
		$ret .= "</table>";
		
		return $ret;
	}
	
	public function summary_subnet()
	{
		$ret = "\t<tr>\n";
		$ret .= sprintf("\t\t<td>%s</td>\n", get_called_class());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getNetworkAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getLowestAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getHighestAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBroadcastAddress());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBitmask());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getBitCount());
		$ret .= sprintf("\t\t<td>%s</td>\n", $this->getHostCount());
		$ret .= "\t</tr>\n";
		
		return $ret;
	}
}

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

	$ipv6 = implode(':', array_map(function($bytes) use ($bin)
	{
		$bin_part = substr($bin, $bytes * 16, 16);
		
		return str_pad(dechex(bindec($bin_part)), 4, '0', STR_PAD_LEFT);
	}, range(0, 7)));

	return $compress ? inet_ntop(inet_pton($ipv6)) : $ipv6;
}

function not($number, $length)
{
	return gmp_xor($number, gmp_sub(gmp_pow(2, $length), 1));
}
