<?php

class ModulesController {

	private $available_methods = ['GET', 'POST', 'DEL'];


	/**
	 * Require auth
	 */

	protected function require_auth($lvl) {
		if ($this->_auth->get_lvl() < $lvl) {
			throw new Exception("Your authentication level is lower than required by this method");
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * Require access method
	 */

	protected function require_request_method($method) {
		if (!in_array($method, $this->available_methods)) {
			throw new Exception("Unknown request method {$method} was required to run controller method");
		}
		elseif ($_SERVER['REQUEST_METHOD'] !== $method) {
			throw new Exception("Controller method requires to be run with request method {$method}");
		}
		else {
			return true;
		}
	}
}