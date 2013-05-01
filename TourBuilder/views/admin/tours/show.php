<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;

head( array( 'title' => $tourTitle, 'content_class' => 'horizontal-nav',
   'bodyclass' => 'show' ) );
?>

<h1><?php echo $tourTitle; ?>
<span class="view-public-page">[ <a href="<?php
echo html_escape( public_uri( 'tour-builder/tours/show/id/' . tour( 'id' ) ) );
?>">View Public Page</a> ]</span></h1>

<?php if( has_permission( 'TourBuilder_Tours', 'edit' ) ): ?>
<p id="edit-tour" class="edit-button"><a class="edit" href="<?php
echo $this->url( array( 'action' => 'edit', 'id' => tour( 'id' ) ) )
?>">Edit this Tour</a></p>
<?php endif; ?>

<div id="primary">
   <div id="tour-slug" class="element">
      <h2>Slug</h2>
      <div class="element-text">
         <?php echo tour( 'Slug' ); ?>
      </div>
   </div>

   <div id="tour-description" class="element">
      <h2>Description</h2>
      <div class="element-text">
         <?php echo nls2p( tour( 'Description' ) ); ?>
      </div>
   </div>

   <div id="tour-credits" class="element">
      <h2>Credits</h2>
      <div class="element-text">
         <?php echo (tour( 'Credits' )) ? tour( 'Credits' ) : 'Credit field is not set'; ?>
      </div>
   </div>
   
   <!--div id="tour-credits" class="element">
      <h2>Thumbnail</h2>
      <div class="element-text">
         <?php// echo ( tour( 'Thumbnail' ) ) ? '<div id="thumb-preview" style="width:200px; height:auto;background:#ccc;"><img style="max-width:100%; height:auto;" src="'.tour( 'Thumbnail' ).'"/></div>' : 'Thumbnail field is not set'; ?>
      </div>
   </div-->   

<div id="tour-items" class="element">
   <h2>Items</h2>
   <div class="element-text">
      <ul>
         <?php foreach( $tour->Items as $tourItem ): ?>
         <li><a href="<?php
         echo uri('items/show/'.$tourItem->id); ?>"><?php
         echo $this->itemMetadata( $tourItem, 'Dublin Core', 'Title' ); ?></a>
         </li>
         <?php endforeach; ?>
      </ul>
   </div>
</div>
</div>

<?php foot();
