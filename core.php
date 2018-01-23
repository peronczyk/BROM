<?php

/**
 * =================================================================================
 *
 * Servant
 * Class : Core
 *
 * =================================================================================
 */

/**
 * DEFAULT CONFIGURATION
 * All keys will be turned to constants.
 * This configuration can be overwritten by file 'config.php' placed in main
 * directory, which should return array with variables that you want to change.
 * Remember that each of the setting names should start with underscore.
 */

$default_config = [
	// This variable will be added to meta section of all api request. It also will
	// be displayed on front page of admin panel
	'_SITE_NAME' => $_SERVER['SERVER_NAME'],

	// Force displaying all errors, warnings and notices
	// by default this mode is turned on when working on localhost
	'_DEBUG' => preg_match('/(localhost|::1|\.dev)$/', @$_SERVER['SERVER_NAME']),

	// Send security headers and remove ones that can potentially expose
	// vulnerabilities. Learn more: https://securityheaders.io
	'_SECURE_HEADERS' => true,

	// Force HTTPS
	'_FORCE_HTTPS' => true,

	// Primary module, that will be displayed to user when he enters root app path
	'_DEFAULT_BASE_MODULE' => 'api',

	// Storage directory
	// It contains SQLite database and uploaded files
	'_STORAGE_DIR' => 'storage/',

	// App directory
	'_APP_DIR' => 'app/',

	// Subdirectories of app directory
	'_ADMIN_DIR' => 'admin/',
	'_API_DIR' => 'api/',
	'_LIBS_DIR' => 'libs/',
	'_MODULES_DIR' => 'modules/',

	// Database file name. You can change this file name to something more complex
	// if you want to be more sure no one will access it from browser.
	'_DB_FILE_NAME' => 'db.sqlite',
];


/**
 * CORE CLASS
 */

class Core {
	public function init() {
		$this->load_configuration();

		error_reporting(_DEBUG ? E_ALL : 0);
		session_start();

		$this->define_paths();
		$this->define_autoloader();

		if (_SECURE_HEADERS) {
			$this->secure_headers();
		}

		if (_FORCE_HTTPS) {
			$this->force_https();
		}
	}

	/** ----------------------------------------------------------------------------
	 * Configuration defines
	 */

	private function load_configuration() {
		$config = $GLOBALS['default_config'];
		if (file_exists('config.php')) {
			$overwrite = include_once('config.php');
			$config = array_merge($config, $overwrite);
		}

		foreach($config as $key => $val) {
			define($key, $val);
		}
	}


	/** ----------------------------------------------------------------------------
	 * App paths definitions required to proper rooting
	 */

	private function define_paths() {

		/**
		 * PROTOCOL (http or https)
		 */

		define('PROTOCOL', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http');


		/**
		 * ROOT URI
		 * This path represents browser location of index.php
		 * Everything after that address is a request.
		 */

		$root_uri = $_SERVER['SERVER_NAME'];
		$script_dirname = dirname($_SERVER['SCRIPT_NAME']);
		if ($script_dirname != '/') $root_uri .= $script_dirname;

		define('ROOT_URI', $root_uri . '/');


		/**
		 * ROOT URL
		 */

		define('ROOT_URL', PROTOCOL . '://' . ROOT_URI);


		/**
		 * REQUEST URI
		 * app_request is created by Mod Rewrite configured in .htaccess file
		 */

		define('REQUEST_URI', @$_GET['app_request']);
	}


	/** ----------------------------------------------------------------------------
	 * Send security headers and remove ones that can potentially expose
	 * vulnerabilities. Learn more: https://securityheaders.io
	 */

	private function secure_headers() {
		// Enables XSS filtering. Rather than sanitizing the page,
		// the browser will prevent rendering of the page if an attack is detected.
		header('X-XSS-Protection: 1; mode=block');

		// Prevent loading page in frames - secures from clickjacking
		header('X-Frame-Options: DENY');

		// This opts-out of MIME type sniffing - is a way to say that the webmasters
		// knew what they were doing.
		// Learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#MIME_sniffing
		header('X-Content-Type-Options: nosniff');

		// Remove header that informs about PHP version
		header_remove('X-Powered-By');
	}


	/** ----------------------------------------------------------------------------
	 * Force using of HTTPS
	 */

	private function force_https() {
		// The HTTP Strict-Transport-Security response header (HSTS) lets a web site
		// tell browsers that it should only be accessed using HTTPS,
		// instead of using HTTP.
		header('Strict-Transport-Security: max-age=31536000; preload');
	}


	/** ----------------------------------------------------------------------------
	 * Autoload libs (PSR-0)
	 */

	public function define_autoloader() {
		spl_autoload_register(function($class) {
			$class_path = __DIR__ . '/' . _APP_DIR . _LIBS_DIR . str_replace('\\', '/', $class) . '.php';
			if (file_exists($class_path)) include_once($class_path);
		});
	}


	/** ----------------------------------------------------------------------------
	 * Get modules list
	 */

	public function get_modules_list() {
		$directories = scandir(_APP_DIR . _MODULES_DIR);
		$modules = [];
		$index = 0;

		foreach($directories as $key => $dir) {
			if ($dir == '.' || $dir == '..' || $dir == 'default') continue;

			$module_config_file = _APP_DIR . _MODULES_DIR . $dir . '/_config.php';

			if (is_file($module_config_file)) {
				$modules[$index] = include_once($module_config_file);
				$modules[$index]['node'] = $dir;
				$index++;
			}
		}

		return $modules;
	}
}