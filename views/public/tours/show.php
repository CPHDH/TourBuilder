<?php echo head( array( 'title' => metadata( 'tour', 'Title' ),
		'bodyclass' => 'tour show' ) ); ?>

<h1><?php echo metadata( 'tour', 'title' ); ?></h1>

<?php $items = $tour->getItems();
if( $items ): ?>
<div id="items" class="element">
  <h3>Locations</h3>
  <?php foreach( $tour->getItems() as $tourItem ):
		set_current_record( 'item', $tourItem );
?>
  <li>
    <?php echo link_to_item(); ?>
  </li>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if( metadata( 'tour', 'Description' ) ): ?>
<div id="tour-description" class="element">
  <h3>Description</h3>
  <div class="element-text">
    <?php echo nls2p( tour( 'Description' ) ); ?>
  </div>
</div>
<?php endif; ?>

<?php if( metadata( 'tour', 'Credits' ) ): ?>
<div id="tour-credits" class="element">
  <h3>Credits</h3>
  <div class="element-text">
    <?php echo tour( 'Credits' ); ?>
  </div>
</div>
<?php endif; ?>

<?php echo foot(); ?>
