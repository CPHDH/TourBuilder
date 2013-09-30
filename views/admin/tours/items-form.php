<?php
   $tourItemCount = count( $tour->Items );
?>
<ul id="tourbuilder-item-list">
  <?php if( $tourItemCount ): ?>
  <table id="tour-items" class="simple" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <th scope="col">Title</th>
        <th scope="col">Move</th>
        <th scope="col">Delete?</th>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php foreach( $tour->Items as $tourItem ):
            $alternator = (++ $key % 2 == 1) ? 'odd' : 'even';
            $itemUri = url( array( 'module' => '',
                                   'controller' => 'items',
                                   'action' => 'show',
                                   'id' => $tourItem->id ) ); ?>
      <tr class="orderable items <?php echo $alternator; ?>">
        <td scope="row">
          <a href="<?php echo $itemUri ?>">
            <?php echo metadata( $tourItem, array( 'Dublin Core', 'Title' ) ); ?>
          </a>
        </td>

        <td scope="row">
          <?php if( $key != 1 ): ?>
      <a class="up" href="<?php echo $this->url( array( 'action' => 'hoistItem',
                                                        'id' => $tour->id,
                                                        'item' => $tourItem->id ) );?>">
        up
      </a>
      <?php endif; ?>

      <?php if( $key != $tourItemCount ): ?>
      <a class="down" href="<?php echo $this->url( array( 'action' => 'lowerItem',
                                                          'id' => $tour->id,
                                                          'item' => $tourItem->id ) ); ?>">
        down
      </a>
      <?php endif; ?>
    </td>

    <td scope="row"><a class="delete" href="<?php echo $this->url(
    array( 'action' => 'removeItem',
    'id' => $tour->id, 'item' => $tourItem->id ) );
    ?>">Remove</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</ul>
<div id="tourbuilder-additem">
<a class="submit" href="<?php echo $this->url(
   array( 'tour' => $tourItem->id, 'action' => 'browseForItem' ) );
?>">Add Item</a>
</div>
