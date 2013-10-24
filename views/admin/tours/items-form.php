<?php
   $tourItemCount = count( $tour->Items );
   $addItemUrl = $this->url(
      array( 'id'   => $tour->id,
             'action' => 'browseForItem' ),
      'tourAction' );
?>
<ul id="tourbuilder-item-list">
  <?php if( $tourItemCount ): ?>
  <table id="tour-items" class="simple" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <th scope="col">
          <?php echo __('Title'); ?>
        </th>
        <th scope="col">
          <?php echo __('Move'); ?>
        </th>
        <th scope="col">
        </th>
      </tr>
    </thead>
    <tbody>
      <?php $key = 0; ?>
      <?php foreach( $tour->Items as $tourItem ):
            $alternator = (++ $key % 2 == 1) ? 'odd' : 'even';
            $itemUri = record_url( $tourItem, 'show', true );
            $itemHoist = $this->url( array( 'action' => 'hoistItem',
                                            'id'     => $tour->id,
                                            'item'   => $tourItem->id ),
                                     'tourItemAction' );
            $itemLower = $this->url( array( 'action' => 'lowerItem',
                                            'id'     => $tour->id,
                                            'item'   => $tourItem->id ),
                                     'tourItemAction' );
            $itemDelete = $this->url( array( 'action' => 'removeItem',
                                             'id'     => $tour->id,
                                             'item'   => $tourItem->id ),
                                      'tourItemAction' );
      ?>
      <tr class="orderable items <?php echo $alternator; ?>">
        <td scope="row">
          <a href="<?php echo $itemUri ?>">
            <?php echo metadata( $tourItem, array( 'Dublin Core', 'Title' ) ); ?>
          </a>
        </td>

        <td scope="row">
          <?php if( $key != 1 ): ?>
          <a class="up" href="<?php echo $itemHoist; ?>">
            <?php echo __('up'); ?>
          </a>
          <?php endif; ?>

          <?php if( $key != $tourItemCount ): ?>
          <a class="down" href="<?php echo $itemLower; ?>">
            <?php echo __('down'); ?>
          </a>
          <?php endif; ?>
        </td>

        <td scope="row">
          <a class="delete" href="<?php echo $itemDelete; ?>">
            <?php echo __('Remove'); ?>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>

    </tbody>
  </table>
  <?php endif; ?>
</ul>

<div id="tourbuilder-additem">
  <a class="submit" href="<?php echo $addItemUrl; ?>">
    <?php echo __('Add Item'); ?>
  </a>
</div>
