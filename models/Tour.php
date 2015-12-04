<?php

require_once 'TourTable.php';

/**
 * Tour
 * @package: Omeka
 */
class Tour extends Omeka_Record_AbstractRecord
{
	public $title;
	public $description;
	public $credits;
	public $featured = 0;
	public $public = 0;
	public $slug;
	public $postscript_text;
	public $tour_image;

	protected $_related = array( 'Items' => 'getItems','Image' => 'getImage' );

	public function getItems()
	{
		return $this->getTable()->findItemsByTourId( $this->id );
	}

	public function getImage() {

	}

	public function afterSave($args) {

	}

	public function hasImage($path = 'original') {
		return file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . 'tour_'.$this->id.'.jpg');
	}

	public function image($tag_wrap = true) {
		$dir = 'original';
		return $this->build_image_path($dir, $tag_wrap);
	}

	public function thumbnail($tag_wrap = true) {
		$dir = 'thumbnails';
		return $this->build_image_path($dir, $tag_wrap);
	}

	public function square_thumbnail($tag_wrap = true) {
		$dir = 'square_thumbnails';
		return $this->build_image_path($dir, $tag_wrap);
	}

	public function build_image_path($dir, $tag_wrap = true) {
		if($this->hasImage($dir)) {
			$return = '/files/'.$dir.'/tour_'.$this->id.'.jpg';
			if($tag_wrap) {
				$return = '<img src="'.$return.'">';
			}
			return $return;
		}
		return '';
	}


	public function removeAllItems( ) {
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );
		$select = $tiTable->getSelect();
		$select->where( 'tour_id = ?', array( $this->id ) );

		# Get the tour item
		$tourItems = $tiTable->fetchObjects( $select );

		# Iterate through all the tour items
		# and remove them
		for($i = 0; $i < count($tourItems); $i++) {
			$tourItems[$i]->delete();
		}
	}

	public function removeItem( $item_id )
	{
		if( !is_numeric( $item_id ) ) {
			$item_id = $item_id->id;
		}

		# First get the tour-item object
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );
		$select = $tiTable->getSelect();
		$select->where( 'tour_id = ?', array( $this->id ) )
		->where( 'item_id = ?', array( $item_id ) );

		# Get the tour item
		$tourItem = $tiTable->fetchObject( $select );

		# Renumber any ordinals greater than it.
		$select = $tiTable->getSelect();
		$select->where( 'tour_id = ?', array( $this->id ) )
		->where( 'ordinal > ?', array( $tourItem->ordinal ) );

		# Delete this linkage
		$tourItem->delete();

		# Reorder the remaining linkages
		$renumbers = $tiTable->fetchObjects( $select );
		foreach( $renumbers as $ti )
		{
			$ti->ordinal = $ti->ordinal - 1;
			$ti->save();
		}

	}

	public function addItem( $item_id, $ordinal = null )
	{
		if( !is_numeric( $item_id ) ) {
			$item_id = $item_id->id;
		}

		# Get the next ordinal
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );
		$select = $tiTable->getSelectForCount();
		$select->where( 'tour_id = ?', array( $this->id ) );
		if($ordinal === null) {
			$ordinal = $tiTable->fetchOne( $select );
		}

		# Create, assign, and save the new tour item connection
		$tourItem = new TourItem;
		$tourItem->tour_id = $this->id;
		$tourItem->item_id = $item_id;
		$tourItem->ordinal = $ordinal;
		$tourItem->save();
	}

	public function saveItemOrder( $tour_id ) {

	}

	public function hoistItem( $tour_id, $item_id )
	{
		$this->swapItem( $tour_id, $item_id, true );
	}

	public function lowerItem( $tour_id, $item_id )
	{
		$this->swapItem( $tour_id, $item_id, false );
	}

	public function setItemOrdinal( $tour_id, $item_id, $ordinal ) {
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );

		// Get the target item
		$select = $tiTable->getSelect()
		->where( 'tour_id = ?', $tour_id )
		->where( 'item_id = ?', $item_id );
		$item = $tiTable->fetchObject( $select );
		$item->ordinal = $ordinal;
		$item->save();
	}

	public function swapItem( $tour_id, $item_id, $up )
	{
		$db = get_db();
		$tiTable = $db->getTable( 'TourItem' );

		// Get the target item
		$select = $tiTable->getSelect()
		->where( 'tour_id = ?', $tour_id )
		->where( 'item_id = ?', $item_id );
		$left = $tiTable->fetchObject( $select );
		$ordinal = intval( $left->ordinal );

		// Get the next item with which we are swapping
		$select = $tiTable->getSelect()
		->where( 'tour_id = ?', $tour_id )
		->where( $up ? 'ordinal < ?' : 'ordinal > ?', $ordinal )
		->limit( 1 );
		$right = $tiTable->fetchObject( $select );

		// Do the ordinal shuffle
		$left->ordinal = intval( $right->ordinal );
		$right->ordinal = $ordinal;

		// Save both items
		$left->save();
		$right->save();
	}

	protected function _validate()
	{
		if( empty( $this->title ) ) {
			$this->addError( 'title', 'Tour must be given a title.' );
		}

		if( strlen( $this->title > 255 ) ) {
			$this->addError( 'title', 'Title for a tour must be 255 characters or fewer.' );
		}
		if (!$this->fieldIsUnique('title')) {
			$this->addError('title', 'The Title is already in use by another tour. Please choose another.');
		}

		if( strlen( $this->slug > 30 ) ) {
			$this->addError( 'slug', 'Slug for a tour must be 30 characters or fewer.' );
		}
		
		if( !empty($this->tour_image) && !is_array(getimagesize( $this->tour_image )) ){
			$this->addError('tour_image','The text entered does not validate as an image URL.');
		}

		if( empty( $this->slug ) ) {
			if($title=$this->title){

			// replace non letter or digits by -
			$title = preg_replace('~[^\\pL\d]+~u', '-', $title);
			
			// trim
			$title = trim($title, '-');
			
			// transliterate
			$title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
			
			// lowercase
			$title = strtolower($title);
			
			// remove unwanted characters
			$title = preg_replace('~[^-\w]+~', '', $title);				
				
				$this->slug= $title;
			}else{
				$this->addError( 'slug', 'Tour must be given a slug.' );
			}
		}
		if (!$this->fieldIsUnique('slug')) {
			$this->addError('slug', 'The slug is already in use by another tour. Please choose another.');
		}
	}
}
