<?php

declare(strict_types=1);

class RestStore {

	// Data storage
	protected $store = [];


	/**
	 * Get store state
	 *
	 * @return object
	 */

	public function get() : array {
		return $this->store;
	}


	/**
	 * Set store variable
	 *
	 * @param string $key
	 * @param $value
	 */

	public function set(string $key, $value) : object {
		$this->store[$key] = $value;
		return $this;
	}


	/**
	 * Merge store with passed variables array
	 *
	 * @param array $array
	 */

	public function merge(array $array) : object {
		$this->store = array_merge($this->store, $array);
		return $this;
	}


	/**
	 * Display JSON
	 */

	public function output() {
		if (!headers_sent()) {
			header('Content-type: application/json');
			header('Access-Control-Allow-Origin: *');
		}
		echo json_encode($this->get());
		exit;
	}
}