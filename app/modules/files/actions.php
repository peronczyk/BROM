<?php

class FilesActions {

	protected $uploads_dir;
	protected $max_depth = 8;

	private $ignored_files = ['.', '..', '.htaccess'];


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct($dependencies) {
		$dependencies->register($this);

		$this->uploads_dir = _STORAGE_DIR . 'uploads/';
	}


	/** ----------------------------------------------------------------------------
	 * Get files list
	 */

	public function get_files_list($location = '') {

		// Validate $location
		if (!empty($location)) {
			if (preg_match('/(\.\.)/i', $location)) {
				throw new Exception("Provided `location` is invalid: `{$location}`");
			}
		}

		$files_dir = trim($this->uploads_dir . $location, '/') . '/';

		if (!is_dir($files_dir)) {
			throw new Exception("Provided `location` does not exist");
		}

		$files_list = scandir($files_dir);
		$files_data = [];

		foreach ($files_list as $key => $file_name) {
			if (!in_array($file_name, $this->ignored_files)) {
				$file_path = $files_dir . $file_name;
				$file_type = mime_content_type($file_path);

				$arr['type'] = $file_type;
				$arr['full-name'] = $file_name;

				// Entries for directories
				if ($file_type == 'directory') {
					$arr['name'] = $file_name;
					$arr['children'] = count(scandir($files_dir . $file_name)) - 2;
				}

				// Entries for files
				else {
					$extension = pathinfo($file_name, PATHINFO_EXTENSION);

					$arr['name']      = str_replace('.' . $extension, '', $file_name);
					$arr['extension'] = $extension;
					$arr['size']      = filesize($file_path);
					$arr['path']      = $file_path;
					$arr['full-path'] = ROOT_URL . $file_path;
				}

				$files_data[] = $arr;
			}
		}
		return $files_data;
	}


	/** ----------------------------------------------------------------------------
	 * Create directory
	 */

	public function create_dir($dir_name, $location) {
		// Check if new directory name was provided
		if (empty($dir_name)) {
			throw new Exception("Name of the directory to be created was not provided.");
		}
		$path_chunks = explode('/', trim($location, '/'));

		// Check if this directory will be too deep
		if (count($path_chunks) > $this->max_depth) {
			throw new Exception("Could not create directory because of maximal upload depth.");
		}

		// Validate each path chunk
		foreach($path_chunks as $chunk) {
			if (strpbrk($chunk, "/?%*:|\"<>") !== FALSE) {
				throw new Exception("Provided directory path is not valid because of illegal characters.");
			}
		}

		$dir_path = $this->uploads_dir . implode('/', $path_chunks) . $dir_name;

		if (file_exists($dir_path)) {
			throw new Exception("Directory with this name exists in provided location.");
		}

		return mkdir($dir_path);
	}


	/** ----------------------------------------------------------------------------
	 * Upload files
	 */

	public function upload($files, $location) {
		return print_r($files, true);
	}


	/** ----------------------------------------------------------------------------
	 * Delete file
	 */

	public function delete_file($file_name, $location) {
		if (empty($file_name)) {
			throw new Exception('File to be removed was not specified.');
		}

		$file_path = $_SERVER['DOCUMENT_ROOT'] . trim($this->uploads_dir . $location, '/') . '/' . $file_name;
		return $file_path;

		if (!file_exists($file_path)) {
			throw new Exception("File `{$file_name}` does not exist in specified location of uploads.");
		}

		$result = @unlink($file_path);

		if ($result) return true;
		else {
			$last_error = error_get_last();
			if (is_array($last_error)) {
				throw new Exception("Error occured while trying to remove file `{$file_path}`: {$last_error['message']}.");
			}
			else {
				throw new Exception("Unknown error occured while trying to remove file `{$file_path}`");
			}
		}
	}
}