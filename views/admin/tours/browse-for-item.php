<?php
/*
$tourTitle = strip_formatting( tour( 'title' ) );
if( $tourTitle != '' && $tourTitle != '[Untitled]' )
{
   $tourTitle = ': &quot;' . $tourTitle . '&quot; ';
}
else
{
   $tourTitle = '';
}
$tourTitle = 'Add Item To Tour #' . tour( 'id' ) . $tourTitle;

echo head( array( 'title' => $tourTitle,
                  'content_class' => 'vertical-nav',
                  'bodyclass' => 'tours primary' ) );
                  */
?>
<div id="primary">
<table id="items" class="simple" cellspacing="0" cellpadding="0">
   <thead>
      <tr>
         <th scope="col">ID</th>
         <th scope="col">Item</th>
         <th scope="col">Add?</th>
      </tr>
   </thead>
   <tbody>
      <?php $key = 0;
            foreach( $this->items as $item ):
               $oddness = ((++ $key % 2) == 1 ) ? 'odd' : 'even';
               $itemName = metadata( $item, array( 'Dublin Core', 'Title' ) );
               $itemUrl = url( array( 'controller' => 'items',
                                      'action' => 'show',
                                      'id' => $item->id ), 'id' );
               $addUrl = url( array( 'action' => 'addItem',
                                     'item' => $item->id,
                                     'id' => $tour->id ), 'tourItemAction' );
      ?>

      <tr class="items <?php echo $oddness; ?>">
         <td scope="row"><?php echo $item->id ?></td>
         <td scope="row">
            <a href="<?php echo $itemUrl; ?>">
              <?php echo $itemName; ?>
            </a>
         </td>
         <td scope="row">
            <a class="add" href="<?php echo $addUrl; ?>">
              <?php echo __('Add to tour'); ?>
            </a>
         </td>
      </tr>
      <?php endforeach; ?>
   </tbody>
</table>
</div>
<?php //echo foot(); ?>
