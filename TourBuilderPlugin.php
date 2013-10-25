<?php

if( !defined( 'TOURBUILDER_PLUGIN_DIR' ) )
{
   define( 'TOURBUILDER_PLUGIN_DIR', dirname( __FILE__ ) );
}

class TourBuilderPlugin extends Omeka_Plugin_AbstractPlugin
{
   protected $_filters = array(
      'admin_dashboard_stats',
      'admin_navigation_main' );

   protected $_hooks = array(
      'install',
      'uninstall',
      'define_acl',
      'define_routes',
      'admin_theme_header' );

   public function hookInstall()
   {
      $db = $this->_db;

      $tourQuery = "
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
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

      $tourItemQuery = "
         CREATE TABLE IF NOT EXISTS `$db->TourItem` (
            `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `tour_id` INT( 10 ) UNSIGNED NOT NULL,
            `ordinal` INT NOT NULL,
            `item_id` INT( 10 ) UNSIGNED NOT NULL,
            PRIMARY KEY( `id` ),
            KEY `tour` ( `tour_id` )
         ) ENGINE=InnoDB ";

      $db->query( $tourQuery );
      $db->query( $tourItemQuery );
   }

   public function hookUninstall()
   {
      $db = $this->_db;
      $db->query( "DROP TABLE IF EXISTS `$db->TourItem`" );
      $db->query( "DROP TABLE IF EXISTS `$db->Tour`" );
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

      // Allow everyone to view tours
      $acl->allow( null, 'TourBuilder_Tours', 'show' );
      $acl->allow( null, 'TourBuilder_Tours', 'browse' );
      $acl->deny(  null, 'TourBuilder_Tours', 'show-unpublished' );
   }

   public function hookDefineRoutes( $args )
   {
      $router = $args['router'];
      $router->addConfig( new Zend_Config_Ini(
                             TOURBUILDER_PLUGIN_DIR .
                             DIRECTORY_SEPARATOR .
                             'routes.ini', 'routes' ) );
   }

   public function filterAdminDashboardStats( $stats )
   {
      if( is_allowed( 'TourBuilder_Tours', 'browse' ) )
      {
         $stats[] = array( link_to( 'tours', array(),
                                    total_records( 'Tours' ) ),
                           __('tours') );
      }
      return $stats;
   }

   public function hookAdminAppendToDashboardPrimary()
   {
      if( has_permission( 'TourBuilder_Tours', 'browse' ) )
      { ?>
   	<dt class="tours"><a href="<?php echo html_escape(uri('tours')); ?>">Tours</a></dt>
   	<dd class="tours">
   		<ul>
   			<li><a class="add-tour use-icon" href="<?php echo html_escape(uri('tour-builder/tours/add/')); ?>">Create a Tour</a></li>
   			<li><a class="browse browse-tour" href="<?php echo html_escape(uri('tour-builder/tours')); ?>">Browse Tours</a></li>
   		</ul>
   		<p>Add and manage mobile tours that display items from the archive.</p>
   	</dd> <?php
      }
   }

   public function hookAdminThemeHeader( $request )
   {
      // Add our stylesheet to admin pages in which we take part
      if( $request->getControllerName() == 'tours' ||
          ($request->getModuleName() == 'default' &&
           $request->getControllerName() == 'index' &&
           $request->getActionName() == 'index') )
      {
         echo '<link rel="stylesheet" media="screen" href="' . html_escape(css('tour')) . '" /> ';
      }
   }

   public function filterAdminNavigationMain( $nav )
   {
      $nav['Tours'] = array( 'label' => __('Tours'),
                             'action' => 'browse',
                             'controller' => 'tours' );
      return $nav;
   }
}


/*
 * Helper functions for use in all themes
 */

function has_tours()
{
   return( total_tours() > 0 );
}

function has_tours_for_loop()
{
   $view = get_view();
   return $view->tours && count( $view->tours );
}


function loop_tours()
{
    return loop_records('tours', get_tours_for_loop(), 'set_current_tour');
}


function tour( $fieldName, $options=array(), $tour=null )
{
   if( ! $tour ) {
      $tour = get_current_tour();
   }

   switch( strtolower( $fieldName ) ) {
      case 'id':
         $text = $tour->id;
         break;
      case 'title':
         $text = $tour->title;
         break;
      case 'description':
         $text = $tour->description;
         break;
      case 'credits':
         $text = $tour->credits;
         break;
      case 'slug':
         $text = $tour->slug;
         break;

      default:
         throw new Exception( "\"$fieldName\" does not exist for tours!" );
         break;
   }

   if( isset( $options['snippet'] ) ) {
      $text = snippet( $text, 0, (int)$options['snippet'] );
   }

   if( !is_array( $text ) ) {
      $text = html_escape( $text );
   } else {
      $text = array_map( 'html_escape', $text );

      if( isset( $options['delimiter'] ) ) {
         $text = join( (string) $options['delimiter'], (array) $text );
      }
   }

   return $text;
}

function set_current_tour( $tour )
{
   get_view()->tour = $tour;
}

function get_current_tour()
{
   return get_view()->tour;
}

function link_to_tour(
   $text=null, $props=array(), $action='show', $tourObj = null )
{
   # Use the current tour object if none given
   if( ! $tourObj ) {
      $tourObj = get_current_tour();
   }

   # Create default text, if it was not passed in.
   if( empty( $text ) ) {
      $tourName = tour('title', array(), $tourObj);
      $text = (! empty( $tourName )) ? $tourName : '[Untitled]';
   }

   return link_to($tourObj, $action, $text, $props);
}

function get_tours_for_loop()
{
   return __v()->tours;
}

function total_tours()
{
   return get_db()->getTable( 'Tours' )->count();
}

function nls2p($str) {
	$str = str_replace('<p></p>', '', '<p>'
        . preg_replace('#([
]\s*?[
]){2,}#', '</p><p>', $str)
        . '</p>');
	return $str;
}

function public_nav_tours( array $navArray = null, $maxDepth = 0 )
{
   if( !$navArray )
   {
      $navArray = array();

      $navArray[] = array(
            'label' => __('Browse All'),
            'uri' => url('tours/browse') );
      /* TODO: Searches not implemented * /
      $navArray[] = array(
         'label' => __('Search Tours'),
         'uri' => url('tours/search') );
      // */
   }

   return nav( $navArray, 'public_navigation_items' );
}