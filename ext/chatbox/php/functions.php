<?php

	function cookie($name, $data) {
		return setcookie($name, $data, time() + 60 * 60 * 24 * 30, '/');
	}

	function cookieGet($name, $default = null) {
		if (isset($_COOKIE[$name]))
			return $_COOKIE[$name];
		else
			return $default;
	}

	function cookieClear($name) {
		setcookie ($name, false, time() - 42);
	}

	function getVar($name) {
		if (isset($_POST[$name])) { return $_POST[$name]; }
		if (isset($_GET[$name])) { return $_GET[$name]; }
		return null;
	}
	
	function clean($s) {
		$s = magic($s);
		$s = htmlspecialchars($s);
		return $s;
	}

	function magic($s) {
		if (get_magic_quotes_gpc()) { $s = stripslashes($s); }
		return $s;
	}
	
	function ip() {
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			return $_SERVER['REMOTE_ADDR'];
	}

	function ipValid($ip) {
		if ($ip == long2ip(ip2long($ip)))		
			return true;		
		return false;
	}

	function validIP($ip) {
		if ($ip == long2ip(ip2long($ip)))
			return true;
		return false;
	}

	function ts() {
		// return microtime(true);
		list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);

	}

	function len($string) {
		$i = 0; $count = 0;
		$len = strlen($string);

		while ($i < $len) {
			$chr = ord($string[$i]);
			$count++;
			$i++;

			if ($i >= $len) break;
			if ($chr & 0x80) {
				$chr <<= 1;
				while ($chr & 0x80) {
					$i++;
					$chr <<= 1;
				}
			}
		}

		return $count;
	}

	function error($err) {
		echo 'Error: ' . $err;
		exit;
	}

	function ys($log = 1) {
		global $yShout, $prefs;
		if ($yShout) return $yShout;

		if (filter_var($log, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => $prefs['logs']))) === false)
		{
		  $log = 1;
		}
		
		$log = 'log.' . $log;
		return new YShout($log, loggedIn());
	}

	function dstart() {
		global $ts;

		$ts = ts();
	}

	function dstop() {
		global $ts;
		echo 'Time elapsed: ' . ((ts() - $ts) * 100000);
		exit;
	}

	function login($hash) {
	//	echo 'login: ' . $hash . "\n";
		
		$_SESSION['yLoginHash'] = $hash;
		cookie('yLoginHash', $hash);
	//	return loggedIn();
	}

	function logout() {
		$_SESSION['yLoginHash'] = '';
		cookie('yLoginHash', '');
//		cookieClear('yLoginHash');
	}

	function loggedIn() {
		global $prefs;

		$loginHash = cookieGet('yLoginHash', false);
//		echo 'loggedin: ' . $loginHash . "\n";
//		echo 'pw: ' . $prefs['password'] . "\n";
		
		if (isset($loginHash)) return $loginHash == md5($prefs['password']);

		if (isset($_SESSION['yLoginHash'])) 
			return $_SESSION['yLoginHash'] == md5($prefs['password']);

		return false;
	}

