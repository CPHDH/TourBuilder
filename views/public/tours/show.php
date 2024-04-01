<?php
$locations=array();
$tourTitle = (tour( 'title' )) ? strip_formatting( tour( 'title' ) ) : '[Untitled]';
echo head( array( 'maptype'=>'tour','title' => ''.__('Tour').' | '.$tourTitle, 'bodyid'=>'tours',
   'bodyclass' => 'show', 'tour'=>$tour ) );
?>
<style>
	.item-result img{}
</style>
<article id="primary" class="tour" role="main">

		<div id="tour-header">
			<?php
			echo '<h1>'.$tourTitle.'</h1>';
			echo '<span class="tour-meta-browse">';
			if(tour( 'Credits' )){
				echo __('Tour curated by: %s',tour( 'Credits' )).' | ';
			}
			echo count($tour->Items).' '.__('Locations').'</span>';
			?>
		</div>

		<div id="tour-text">
			<div id="tour-description">
				<?php echo htmlspecialchars_decode(nls2p( tour( 'Description' ) )); ?>
			</div>
			<div id="tour-postscript">
				<em><?php echo htmlspecialchars_decode(metadata('tour','Postscript Text')); ?></em>
			</div>
		</div>
		
		<div id="tour-items">
			<h2 class="locations"><?php echo $tour->getItems() ? __('Locations for Tour') : null;?></h2>

			<?php 
			$i=1;
			foreach( $tour->getItems() as $tourItem ): 
				if($tourItem->public || current_user()){
					set_current_record( 'item', $tourItem );
					$itemID=$tourItem->id;
					$more='<a href="/items/show/'.$tourItem->id.'">'.__('Learn more').'</a>';
					$hasImage=metadata($tourItem,'has thumbnail');
					$subtitle=metadata($tourItem,array('Item Type Metadata','Subtitle'));
					$description=snippet(metadata($tourItem,array('Item Type Metadata','Story')),0,300,'&hellip; '.$more);
					$custom = $tour->getTourItem($tourItem->id);
					if(!empty($custom->text)){
						$description = $custom->text.' '.$more;
					}
					if(!empty($custom->subtitle)){
						$subtitle = $custom->subtitle;
					}
					$item_image=null;
					if ($hasImage){
						preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('fullsize'), $result);
						$item_image = array_pop($result);
					}
					?>
					<div class="item-result <?php echo $hasImage ? 'has-image' : null;?>" >
						<h3><a class="permalink" href="
							<?php echo url('/') ?>items/show/<?php echo $itemID.'?tour='.tour( 'id' ).'&index='.($i-1).''; ?>">
								<?php echo '<span class="number">'.$i.'.</span>';?> 
								<?php echo metadata( $tourItem, array('Dublin Core', 'Title') ); ?>
								<?php echo $subtitle ? '<span class="sep">:</span> <span class="subtitle">'.$subtitle.'</span>' : null;?></a></h3>
						
						<?php
						echo isset($item_image) ? '<a href="'. url('/') .
						'items/show/'.$itemID.'?tour='.tour( 'id' ).'&index='.($i-1).'"><img src="'.$item_image.'" loading="lazy"/></a>' : null; 
						?>

						<div class="item-description">
							<?php echo '<p>'.$description.'</p>'; ?>
					    </div>
					</div>
					<?php 
					$i++;
					$item_image=null;
				}
			endforeach; ?>
		</div>

</article>

<?php echo foot(); ?>