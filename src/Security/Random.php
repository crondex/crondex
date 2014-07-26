<?php namespace Crondex\Security;

/**
  * This is taken from PasswordHash.php written
  * by http://www.openwall.com/phpass/ (v.3)
  *
  * We're just using the get_random_bytes($count) method
  *
  */

use Crondex\Security\RandomInterface;

class Random implements RandomInterface {

	private $random_state;

	public function get_random_bytes($count)
	{
		$output = '';
		if (is_readable('/dev/urandom') &&
		    ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}
}

