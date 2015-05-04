<?php
require_once 'Tour.php';
require_once 'TourItem.php';

class TourBuilder_ToursController extends Omeka_Controller_AbstractActionController
{
	public function init()
	{
		$this->_helper->db->setDefaultModelName( 'Tour' );
	}

	public function removeitemAction()
	{
		// Get the tour and item id from the request
		$tour = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		// Remove the item (id) from the tour
		$tour->removeItem( $item_id );

		// Go back to editing the tour.
		$this->_redirectToEdit();
	}

	public function getitemsAction() {
		$db = get_db();
		$prefix=$db->prefix;
		$tour = $this->_helper->db->findById();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			"SELECT i.*, (SELECT count(*) FROM ".$prefix."tour_items ti WHERE ti.item_id = i.id AND ti.tour_id = ?) AS `in_tour`
         FROM ".$prefix."items i",
			array( $tour->id ) );

		foreach($items as $key => $arr) {
			$items[$key]['name'] = metadata( $arr, array( 'Dublin Core', 'Title' ) );
			$items[$key]['uri'] = record_url( $arr, 'show', true );
		}

		$itemsName = $this->view->pluralize( 'item' );
		$tourName = $this->view->singularize( $this->_helper->db->getDefaultModelName() );
		$this->view->assign( compact( 'items', 'tour' ) );
	}

	public function browseforitemAction()
	{
		$db = get_db();
		$prefix=$db->prefix;
		$tour = $this->_helper->db->findById();

		# Get all items which are not already in this tour.
		$itemTable = $db->getTable( 'Item' );
		/* This did not work, much as I preferred
      $iAlias = $itemTable->getTableAlias();
      $select = $itemTable->getSelect();
      $select->joinLeft( array( 'ti' => $db->TourItem ),
         "ti.item_id = $iAlias.id AND ti.tour_id = ?" );
      $select->where( 'ti.id IS NULL' );
       */

		# Attach the items to the view
		#$items = $itemTable->fetchObjects( $select, array( $tour_id ) );
		$items = $itemTable->fetchObjects( "SELECT i.*
         FROM ".$prefix."items i LEFT OUTER JOIN ".$prefix."tour_items ti
         ON i.id = ti.item_id AND ti.tour_id = ?
         WHERE ti.id IS NULL",
			array( $tour->id ) );

		$itemsName = $this->view->pluralize( 'item' );
		$tourName = $this->view->singularize( $this->_helper->db->getDefaultModelName() );
		$this->view->assign( compact( 'items', 'tour' ) );
	}

	public function additemAction()
	{
		# Get the tour and item ids
		$tour = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$tour->addItem( $item_id );

		$this->_redirectToEdit();
	}

	public function hoistitemAction()
	{
		$tour = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$tour->hoistItem( $tour->id, intval( $item_id ) );
		$this->_redirectToEdit();
	}

	public function loweritemAction()
	{
		$tour = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$tour->lowerItem( $tour->id, intval( $item_id ) );
		$this->_redirectToEdit();
	}

	# Called only by AJAX at this point in time
	# so I don't do any setting of anything for the
	# view.
	public function savetouritemsAction() {
		$tour = $this->_helper->db->findById();

		# Remove all of the items in the tour
		$tour->removeAllItems();

		# Get our POST of the saveOrder
		$post = $this->getRequest()->getPost();
		$aOrder = json_decode($post['saveOrder'],true);

		# Iterate through all of the tour items
		# passed in an add them to the tour
		for($i = 0; $i < count($aOrder); $i++) {
			$item_id = intval( $aOrder[$i] );
			$tour->addItem( $item_id, $i );
		}
	}

	private function _redirectToEdit()
	{
		$tour_id = $this->getRequest()->getParam( 'id' );
		$this->_helper->redirector->gotoRoute(
			array( 'action' => 'edit',
				'id' => $tour_id ),
			'tourAction' );
	}
}
