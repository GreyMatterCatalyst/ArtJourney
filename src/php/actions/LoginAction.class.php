<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'views/LoginView' );

/**
 * This class implements an action which provides login functionality.
 * @author craigb
 */
class LoginAction extends IndexRoutingItem
{
    const POST_PARAM_USERNAME = 'uf_1';
    const POST_PARAM_PASSWORD = 'pf_1';
    /**
     * Constructs a new logout action object.
     */
    public function __construct( )
    {
	parent::__construct( FALSE );
    }

    /**
     * This abstract function returns the routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'Login';
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
	$returnUrl = UrlFormatter::formatRoutingItemUrl( 'actions/LoginAction' );
	$imageSearchViewUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageSearchView' );

	// if the user is already logged in redirect to the image search view
	if ( $user != NULL )
	{
	    header( "Location: $imageSearchViewUrl" );
	    return;
	}
	else
	{
	    if ( !isset( $_POST[self::POST_PARAM_USERNAME] ) || !isset( $_POST[self::POST_PARAM_USERNAME] ) ) {
		$loginView = new LoginView( );
		$loginView->processRequest( );
		exit;
	    }

	    $username = $_POST[self::POST_PARAM_USERNAME];
	    $password = $_POST[self::POST_PARAM_PASSWORD];
	    $user = User::loadUserByUsername( DbConnectionUtil::getDbConnection( ), $username );

	    if ( $user == NULL || $user->getPassword( ) != md5( $password ) ) {
		$loginView = new LoginView( );
		$loginView->setPreviousAttemptErrorMessage( "Login Failed");
		$loginView->processRequest( );
		exit;
	    }
	    else {
		UserAuthUtil::registerAuthenticatedUser( $user );
		header( "Location: $imageSearchViewUrl" );
		return;
	    }
	}
    }
}