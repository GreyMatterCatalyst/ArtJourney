<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'util/UserAuthUtil' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );

/**
 * This class implements an action which provides logout functionality.
 * @author craigb
 */
class LogoutAction extends IndexRoutingItem
{
    /**
     * Constructs a new logout action object.
     */
    public function __construct( )
    {
	parent::__construct( TRUE );
    }

    /**
     * This abstract function returns the routing key for the implementing class.
     * @return String The index routing key for the implementing class.
     */
    public static function getRoutingKey( )
    {
	return 'Logout';
    }

    /**
     * This abstract function performs the processing of the user request.
     * @param User $user The requesting user. If the user is not null, then by convention
     * actions will assume the user is authenticated, otherwise not.
     * @throws Exception If an error was encountered while processing this request, an exception
     * will be thrown.
     * @return void
     */
    public function processRequest( User $user = NULL )
    {
	if ( $user != NULL )
	    UserAuthUtil::unregisterAuthenticatedUser( $user );
	$landingPageUrl = UrlFormatter::formatRoutingItemUrl( 'views/LandingPageView' );
	header( "Location: $landingPageUrl" );
    }
}