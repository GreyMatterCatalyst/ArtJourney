<?php
/**
 * This script provides the central routing (controller) functionality for the web application.
 * It maps actions/views to request arguments.
 * @author craigb
 */

//print( "hello" );
//exit( );

/** Includes **/
define( 'SRC_PATH', '/home/art_journey/src/php' );
// util includes
require_once( SRC_PATH . '/util/ClassLoader.class.php' );
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/UserAuthUtil' );
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );

/** Main **/

// if the application is running in debug mode, enable error strict reporting
if ( Settings::getSetting( 'DEBUG_MODE' ) ) {
    error_reporting( E_ALL & ~E_STRICT);
}

// start/resume the session
session_start( );

// The index routing item map: maps index routing keys => index routing item objects
$indexRoutingItemMap = array( );

// map the actions
loadAndMapIndexRoutingItem( 'actions/AccessImageAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/AccessStyleSheetAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/AccessJavascriptAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/LoginAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/LogoutAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/EditImageAction', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'actions/SaveJourneyAction', $indexRoutingItemMap );

// map the views
loadAndMapIndexRoutingItem( 'views/ImageCollectionSearchView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/ImageSearchView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/EditImageView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/ImageDetailsView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/ImageSearchResultsView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/LoginView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/StartJourneyView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/JourneyStepView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/FinishJourneyView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/JourneyDetailsView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/ListJourneysView', $indexRoutingItemMap );
loadAndMapIndexRoutingItem( 'views/AboutView', $indexRoutingItemMap );

// this stores the default landing page routing item
$defaultIndexRoutingItem = loadAndMapIndexRoutingItem( 'views/LandingPageView', $indexRoutingItemMap );
$user = UserAuthUtil::getAuthenticatedUser( );

// attempt to parse an index routing item from the request, if one exists route it to the landing page
if ( isset( $_GET[IndexRoutingItem::INDEX_ROUTING_ITEM_GET_PARAM] ) )
{
    $requestedIndexRoutingItemKey = $_GET[IndexRoutingItem::INDEX_ROUTING_ITEM_GET_PARAM];
    if ( !empty( $requestedIndexRoutingItemKey ) && isset( $indexRoutingItemMap[$requestedIndexRoutingItemKey] ) )
    {
	
	$indexRoutingItem = $indexRoutingItemMap[$requestedIndexRoutingItemKey];
	

	// verify authentication standards are met for the index routing item
	if ( $indexRoutingItem->requiresAuthentication( ) )
	{
	    // if the user isn't authenticated, redirect to the default landing page
	    if ( $user == NULL )
	    {
		$defaultIndexRoutingItem->processRequest( NULL );
		exit;
	    }

	    // verify authorization standards are met for the routing item
	    if ( !$indexRoutingItem->isUserAuthorized( $user ) )
	    {
		// TODO redirect to an error page with the message that the requested page requires authorization
		print( "wrong role: " . $user->getUsername( ) );
		exit;
	    }
	}

	// at this point it is assumed either authentication wasn't required, or authentication/authorization standards were met, process the request
	$indexRoutingItem->processRequest( $user );
	exit;
    }
}
// otherwise route the requester to the landing page
$defaultIndexRoutingItem->processRequest( $user );

/** Helper Functions **/

/**
 * This function loads and maps the index routing item which corresponds to the specified action class path into
 * the specified index routing item map.
 * @param String $classPath The class path of the index routing item to be loaded.
 * @param array &$indexRoutingItemMap A reference to the index routing item map which the loaded action
 * will be mapped into.
 * @return IndexRoutingItem The index routing item which was created.
 */
function loadAndMapIndexRoutingItem( $classPath, array &$indexRoutingItemMap )
{
    ClassLoader::requireClassOnce( $classPath );
    $className = ClassLoader::parseClassName( $classPath );
    $newIndexRoutingItem = new $className( );
    $indexRoutingItemMap[$className::getRoutingKey( )] = $newIndexRoutingItem;
    return $newIndexRoutingItem;
}


?>