<?php

declare(strict_types=1);

class FilesController {

	private $actions;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct($dependencies) {
		$dependencies->register($this);

		require 'files_helpers.php';

		require 'FilesActions.php';
		$this->actions = new FilesActions($dependencies);
	}


	/** ----------------------------------------------------------------------------
	 * List
	 */

	public function getList() {
		$location = (!empty($_GET['location'])) ? $_GET['location'] : null;

		$files_list = $this->actions->getFilesList($location);
		$this->_rest->set('data', $files_list);
	}


	/** ----------------------------------------------------------------------------
	 * Create directory
	 */

	public function createDir() {
		$dir_name = (!empty($_POST['name']))     ? $_POST['name']     : null;
		$location = (!empty($_POST['location'])) ? $_POST['location'] : null;

		$result = $this->actions->createDir($dir_name, $location);
		$this->_rest->set('success', $result);
	}


	/** ----------------------------------------------------------------------------
	 * Upload files
	 */

	public function uploadFile() {
		$result = $this->actions->upload($files, $location);
		$this->_rest->set('success', $result);
	}


	/** ----------------------------------------------------------------------------
	 * Delete file or directory
	 */

	public function deleteFile() {
		$location = (!empty($_POST['location'])) ? $_POST['location'] : null;

		$result = $this->actions->deleteFile($_FILES, $location);
		$this->_rest->set('success', $result);
	}
}