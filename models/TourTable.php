<?php

class TourTable extends Omeka_Db_Table
{
   public function findItemsByTourId( $tour_id )
   {
      $db = get_db();

      $itemTable = $this->getTable( 'Item' );
      $select = $itemTable->getSelect();
      $iAlias = $itemTable->getTableAlias();
      $select->joinInner( array( 'ti' => $db->TourItem ),
         "ti.item_id = $iAlias.id", array() );
      $select->where( 'ti.tour_id = ?', array( $tour_id ) );
      $select->order( 'ti.ordinal ASC' );

      $items = $itemTable->fetchObjects( $select );
      return $items;
   }

   public function getSelect()
   {
      $select = parent::getSelect();

      $permissions = new Omeka_Db_Select_PublicPermissions( 'TourBuilder_Tours' );
      $permissions->apply( $select, 'tours', null );

      if( is_allowed( 'TourBuilder_Tours', 'show-unpublished' ) )
      {
         // Determine public level TODO: May be outdated
         $select->where( $this->getTableAlias() . '.public = 1' );
      }

      return $select;
   }

}
