<?php
require_once 'Tour.php';
require_once 'TourItem.php';

class TourBuilder_ToursController extends Omeka_Controller_AbstractActionController
{
	public function init()
	{
		$this->_helper->db->setDefaultModelName( 'Tour' );
	}

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
	
}
