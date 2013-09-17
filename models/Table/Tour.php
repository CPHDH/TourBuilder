<?php

class TourTable extends Omeka_Db_Table
{
   public function getSelect()
   {
      $select = parent::getSelect();
      $permissions = new Omeka_Db_Select_PublicPermissions( 'TourBuilder_Tours' );
      $permissions->apply( $select, 'tours', null );
      return $select;
   }

   public function findItemsByTourId( $tour_id )
   {
      $db = $this->getDb();

      $iTable = $this->getTable( 'Item' );
      $select = $iTable->getSelect();
      $iAlias = $iTable->getTableAlias();
      $select->joinInner( array( 'tour_item' => $db->TourItem ),
                          "tour_item.item_id = $iAlias.id");
      $select->where( 'tour_item.tour_id = ?' );
      $select->order( 'tour_item.ordinal ASC' );

      $items = $this->fetchObjects( $select, array( $tour_id ) );
      return $items;
   }

}
