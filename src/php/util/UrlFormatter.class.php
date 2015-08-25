<?php
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'actions/AccessImageAction' );

/**
 * This class provides utility functions for formatting URLs for this application.
 * @author craigb
 */
class UrlFormatter
{
    private static $baseUrl = NULL;

    /**
     * This method formats a URL for the specified routing item class name, incorporating
     * the get parameters specified in the get parameter map.
     * @param String $routingItemClassPath The class path of the target routing item.
     * @param array $getParamMap A map of get parameters to be included in the URL (optional).
     * @return String The formatted URL.
     */
    public static function formatRoutingItemUrl( $routingItemClassPath, array $getParamMap = NULL )
    {
	ClassLoader::requireClassOnce( $routingItemClassPath );
	$routingClassName = ClassLoader::parseClassName( $routingItemClassPath );
	$url = UrlFormatter::getBaseUrl( ) . '?' . IndexRoutingItem::INDEX_ROUTING_ITEM_GET_PARAM . '=';
	$url .= $routingClassName::getRoutingKey( );
	if ( $getParamMap != NULL )
	{
	    foreach( $getParamMap as $key => $value )
		$url .= "&$key=$value";
	}
	return $url;
    }

    /**
     * Formats a URL for the specified image path.
     * @param String $imagePath The image path to be formatted.
     * @return String The formatted url.
     */
    public static function formatImageUrl( $imagePath )
    {
	$getParamMap = array( AccessImageAction::RELATIVE_IMAGE_PATH_GET_PARAM => $imagePath );
	$url = UrlFormatter::formatRoutingItemUrl( 'actions/AccessImageAction', $getParamMap );
	return $url;
    }

    /**
     * Formats and returns the base URL for the application.
     * @return String The base URL for the application.
     */
    public static function getBaseUrl( )
    {
	if ( UrlFormatter::$baseUrl == NULL )
	    UrlFormatter::$baseUrl = Settings::getSetting( 'APPLICATION_URL' ) . 'index.php';

	return UrlFormatter::$baseUrl;
    }

	    
}
?>