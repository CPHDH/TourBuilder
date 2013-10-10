<?php
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' ) {
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
} else {
   $tourTitle = '';
}
$tourTitle = 'Tour #' . tour( 'id' ) . $tourTitle;

echo head( array( 'title' => $tourTitle,
                  'bodyclass' => 'tour show' ) );
echo flash();
?>

<section class="seven columns alpha">
<div id="primary">
   <?php if( $tour->slug ): ?>
   <div id="tour-slug" class="element">
      <h2>Slug</h2>
      <div class="element-text">
         <?php echo tour( 'Slug' ); ?>
      </div>
   </div>
   <?php endif; ?>

   <?php if( metadata( 'tour', 'Description' ) ): ?>
   <div id="tour-description" class="element">
      <h2>Description</h2>
      <div class="element-text">
         <?php echo nls2p( metadata( 'tour', 'Description' ) ); ?>
      </div>
   </div>
   <?php endif; ?>

   <?php if( metadata( 'tour', 'Credits' ) ): ?>
   <div id="tour-credits" class="element">
      <h2>Credits</h2>
      <div class="element-text">
         <?php echo metadata( 'tour', 'Credits' ); ?>
      </div>
   </div>
   <?php endif; ?>

   <?php
     $items = $tour->getItems();
     if( $tour->getItems() ): ?>
   <div id="tour-items" class="element">
     <h2>Items</h2>
     <div class="element-text">
       <ul>
         <?php foreach( $items as $item ):
           set_current_record( 'item', $item, true );
         ?>
         <li>
           <?php echo link_to_item(); ?>
         </li>
         <?php endforeach; ?>
      </ul>
   </div>
   <?php endif; ?>
</div>
</div>
</section>

<section class="three columns omega">
  <div id="edit" class="panel">
    <?php if( is_allowed( 'TourBuilder_Tours', 'edit' ) ): ?>
    <a href="<?php echo url( array( 'action' => 'edit', 'id' => $tour->id ) ); ?>"
       class="edit big green button" target="_blank">
      <?php echo __('Edit'); ?>
    </a>
    <?php endif; ?>
  </div>
</section>

<?php echo foot(); ?>
