<?php

if( !defined( 'TOUR_PLUGIN_DIR' ) )
{
   define( 'TOUR_PLUGIN_DIR', dirname( __FILE__ ) );
}

class TourBuilderPlugin extends Omeka_Plugin_AbstractPlugin
{
   protected $_hooks = array(
      'install',
      'uninstall',
      'define_acl',
      'define_routes',
      'admin_dashboard',
      'admin_head' );

   protected $_filters = array(
      'admin_navigation_main' );

   public function hookInstall()
   {
      $db = $this->_db;
      $db->exec( <<<SQL
         CREATE TABLE IF NOT EXISTS `$db->Tour` (
            `id` int( 10 ) unsigned NOT NULL auto_increment,
            `title` varchar( 255 ) collate utf8_unicode_ci default NULL,
            `description` text collate utf8_unicode_ci NOT NULL,
            `credits` text collate utf8_unicode_ci,
            `featured` tinyint( 1 ) default '0',
            `public` tinyint( 1 ) default '0',
            `slug` varchar( 30 ) collate utf8_unicode_ci default NULL,
            PRiMARY KEY( `id` ),
            UNIQUE KEY `slug` ( `slug` )
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL
      );
      $db->exec( <<<SQL
         CREATE TABLE IF NOT EXISTS `$db->TourItem` (
            `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `tour_id` INT( 10 ) UNSIGNED NOT NULL,
            `ordinal` INT NOT NULL,
            `item_id` INT( 10 ) UNSIGNED NOT NULL,
            PRIMARY KEY( `id` ),
            KEY `tour` ( `tour_id` )
         ) ENGINE=MyISAM
SQL
      );
   }

   public function hookUninstall()
   {
      $db = $this->_db;
      $db->exec( "DROP TABLE IF EXISTS $db->Tour" );
      $db->exec( "DROP TABLE IF EXISTS $db->TourItem" );
   }

   public function hookDefineAcl( $args )
   {
      $acl = $args['acl'];

      // Create the ACL context
      $resourceAcl = new Zend_Acl_Resource( 'TourBuilder_Tours' );
      $acl->add( $resourceAcl );

      // Allow administrative (and better) to do anything with tours
      $acl->allow( array( 'super', 'admin' ),
                   'TourBuilder_Tours' );

      // Allow everyone else only to view tours
      $acl->allow( null,
                   'TourBuilder_Tours',
                   array( 'show' ) );
      $acl->deny( null, 'TourBuilder_Tours',
                  'show-unpublished' );
   }

   public function hookDefineRoutes( $args )
   {
      $router = $args['router'];

      $router->addConfig( new Zend_Config_Ini(
                             TOUR_PLUGIN_DIR
                             . DIRECTORY_SEPARATOR
                             . 'routes.ini',
                             'routes' ) );

      $singleRoute = new Zend_Controller_Router_Route(
         'tours/:action/:id',
         array( 'controller' => 'tours',
                'module' => 'TourBuilder',
                'id' => '1' ),
         array( 'id' => '\d+' ) );
      $router->addRoute( 'tours', $singleRoute );

      $singleIdRoute = new Zend_Controller_Router_Route(
         'tours/:action/id/:id',
         array( 'module' => 'TourBuilder',
                'controller' => 'tours',
                'id' => '1' ),
         array( 'id' => '\d+' ) );
      $router->addRoute( 'tours_single_id', $singleRoute );

      $collectionRoute = new Zend_Controller_Router_Route(
         'tours/:action',
         array( 'module' => 'TourBuilder',
                'controller' => 'tours',
                'action' => 'browse' ),
         array() );
      $router->addRoute( 'tours_collection', $collectionRoute );
   }

   public function hookAdminDashboard( $stats )
   {
      if( is_allowed( 'TourBuilder_Tours', 'browse' ) )
      {
         $stats[] = array( link_to( 'tours',
                                    array(),
                                    total_records( 'Tours' ),
                                    __('tours') ) );
      }

      return $stats;
   }

   public function hookAdminHead()
   {
      $request = Zend_Controller_Front::getInstance()->getRequest();

      if( $request->getControllerName() == 'tours'
          || ($request->getModuleName() == 'default' &&
              $request->getControllerName() == 'index' &&
              $request->getActionName() == 'index') )
      {
         queue_css_file( 'tour' );
      }
   }

   public function filterAdminNavigationMain( $nav )
   {
      $nav['Tours'] = array( 'label' => __('Tours'),
                             'uri' => url( 'tours' ) );
      return $nav;
   }

   public function filterPublicNavigationMain( $nav )
   {
      $nav['Tours'] = array( 'label' => __('Tours'),
                             'uri' => url( 'tours' ) );
      return $nav;
   }
}
