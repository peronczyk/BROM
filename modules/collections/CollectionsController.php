<?php

class CollectionsController extends ModulesController {

	private $actions;


	/** ----------------------------------------------------------------------------
	 * Constructor
	 */

	public function __construct($dependencies) {
		$dependencies->register($this);

		include('CollectionsActions.php');
		$this->actions = new CollectionsActions($dependencies);
	}


	/** ----------------------------------------------------------------------------
	 * List
	 */

	public function get_list() {
		$collections_list = $this->_db->select()->from('collections')->all();
		foreach($collections_list as $key => $val) {
			$collections_list[$key]['fields'] = json_decode($val['fields']);
		}
		$this->_rest->set('collections-list', $collections_list);
	}


	/** ----------------------------------------------------------------------------
	 * Add
	 */

	public function add() {
		/** @todo */
	}


	/** ----------------------------------------------------------------------------
	 * Remove
	 */

	public function remove() {
		/** @todo */
	}
}