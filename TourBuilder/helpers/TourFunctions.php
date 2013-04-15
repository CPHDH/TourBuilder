<?php
/*
** JSON tour list output
*/
function display_toursList() {
	// Start with an empty array of tours
	$all_tours_metadata = array();
	
	// Loop through all the tours
	while( loop_tours() ) {
	   $tour = get_current_tour();
	
	   $tour_metadata = array( 
	      'id'     => tour( 'id' ),
	      'title'  => tour( 'title' ),
	   );
	
	   array_push( $all_tours_metadata, $tour_metadata );
	}
	
	$metadata = array(
	   'tours'  => $all_tours_metadata,
	);
	
	// Encode and send
	echo Zend_Json_Encoder::encode( $metadata );
}
/*
** Display the Tours list
*/
function display_random_tours($num = 10){
	
    // Get the database.
    $db = get_db();

    // Get the Tour table.
    $table = $db->getTable('Tour');

    // Build the select query.
    $select = $table->getSelect();
    $select->from(array(), 'RAND() as rand');
    $select->where('public = 1');
	 
    // Fetch some items with our select.
    $items = $table->fetchObjects($select);
    shuffle($items);
    $num = (count($items)<$num)? count($items) : $num;
   
    echo '<h2>Take a Tour</h2>';
    
	for ($i = 0; $i < $num; $i++) {
    	echo '<article class="item-result">';
    	echo '<h3 class="home-tour-title"><a href="' . WEB_ROOT . '/tour-builder/tours/show/id/'. $items[$i]['id'].'">' . $items[$i]['title'] . '</a></h3>';
    	
    	//<div class="item-description">'.snippet($items[$i]['description'],0,250,"...").'</div>';
    	
    	echo '</article>';
	}
	
	echo '<p class="view-more-link"><a href="'.WEB_ROOT.'/tour-builder/tours/browse/">View All Tours</a></p>';
	
	return $items;
}
/* 
** Validates a URL
** Used to make sure the user-submitted tour thumbnail URL is pattern-valid
** TODO: this needs to actually detect a usable image file, but the URL will work for now...
*/
function isValidURL($url)
{
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

/* 
** 	Basic tour info
*/
function tour_credit(){
	return html_escape(tour('Credit'));
}
function tour_link(){
	return html_escape( public_uri( 'tour-builder/tours/show/id/' . tour( 'id' ) ) );
}
function tour_description(){
	return nls2p( tour( 'Description' ) );
	}	
	
//function tour_thumb(){
//	return html_escape(tour('Thumbnail')); 
//}

/* 
** Display the thumb for the tour.
** Used to generate slideshow, etc.
** TODO: expand $userDefined option to encompass either a user-set globally-defined img URL or a user-set tour-specific img URL
** USAGE: display_tour_thumb($this->tour,0) 
*/
function display_tour_thumb($tour,$i,$userDefined=null){ 

	$firstTourItem=tour_item_id($tour,$i);
	
	$html='<div class="item-thumb hidden">';
	$html .= '<a href="'.html_escape(public_uri('tour-builder/tours/show/id/'.tour('id'))).'">';

	if($userDefined){
		$html .= '<img src="'.$userDefined.'"/>';
		
	}elseif($firstTourItem){
		// use the thumb for the first item in the tour
		$item = get_item_by_id($firstTourItem);
		$html .= item_square_thumbnail($props = array(),$index = 0, $item);

	}else{
		// use the fallback if their are no items in the tour
		$html .= '<img src="'.public_uri('plugins/TourBuilder/views/public/images/default_thumbnail.png').'"/>';
	}
	
	$html .= '</a></div>';
	
	return $html;
}
/*
** Get an ID of an item in a tour
** $tour sets the tour, usually via something like $this->tour
** $i is used to choose which position in the item array should be used, usually 0	
** USAGE: tour_item_id($this->tour,0) 
*/
function tour_item_id($tour,$i){
	$toursIDs=array();
	foreach( $tour->Items as $tourItem ){
		array_push($toursIDs,$tourItem->id);
	}
	return $toursIDs[$i];
}
/*
** Get tour object
*/
function get_tour_by_id($tourID)
{
    return get_db()->getTable('Tour')->find($tourID);
}

function tour_nav(){
	if ( (isset($_GET['tour'])) && (isset($_GET['index'])) ){
		
		$tour=$_GET['tour'];
		$currentIndex=$_GET['index']; 
		$prevIndex=$_GET['index']-1;
		$nextIndex=$_GET['index']+1;
		
		$tourTitle=get_tour_by_id($tour)->title;
		$tourURL=html_escape(public_uri('tour-builder/tours/show/id/'.$tour));
		
		$prev = (tour_item_id(get_tour_by_id($tour),$prevIndex)) ? tour_item_id(get_tour_by_id($tour),$prevIndex) : false;
		$next = (tour_item_id(get_tour_by_id($tour),$nextIndex)) ? tour_item_id(get_tour_by_id($tour),$nextIndex) : false;
		$current = (tour_item_id(get_tour_by_id($tour),$currentIndex)) ? tour_item_id(get_tour_by_id($tour),$currentIndex): false;
		
		html_escape(public_uri('items/show/'.$prev.'?tour='.$tour.'&index='.$prevIndex));
		html_escape(public_uri('items/show/'.$next.'?tour='.$tour.'&index='.$nextIndex));
		
			
		$html.='<div class="tour-nav">';
		$html.='Tour navigation:&nbsp;&nbsp;';
		$html.='<span id="tour-nav-links">';
			$html.= ($prev) ? '<a title="Previous stop on tour" href="'.$prev.'?tour='.$tour.'&index='.$prevIndex.'">Previous</a> | ' :'';
			$html.= ($tourURL) ? '<a href="'.$tourURL.'" title="View tour: '.$tourTitle.'">Tour Info</a> ' : '';
			$html.= ($next) ? '| <a title="Next stop on tour" href="'.$next.'?tour='.$tour.'&index='.$nextIndex.'">Next</a>' : '';
		$html.='</span>';
		$html .='<span id="close"><a>X</a></span>';
		$html.='</div>';
		
		return $html.'<script>jQuery("span#close").click(function() {
			jQuery(".tour-nav").fadeOut("fast","linear");});</script>';
		
	}
}

?>