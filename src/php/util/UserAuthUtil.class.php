<?php
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'util/Settings' );

/**
 * This class provides utility functions for verifying user authentication.
 * @author craigb
 */
class UserAuthUtil
{
    /**
     * Checks whether or not the requester is an authenticated user.
     * @return boolean Whether or not the requester is an authenticated user.
     */
    public static function isRequesterAuthenticated( )
    {
	return isset( $_SESSION['AUTHENTICATED_USER'] );
    }

    /**
     * If the requester is authenticated, returns a reference to the user object associated
     * with the requester.
     * @return A reference to the user object associated with the requester, or NULL if the requester isn't
     * authenticated.
     */
    public static function getAuthenticatedUser( )
    {
	if ( UserAuthUtil::isRequesterAuthenticated( ) )
	    return $_SESSION['AUTHENTICATED_USER'];
	else
	    return NULL;
    }

    public static function registerAuthenticatedUser( User $user )
    {
	$_SESSION['AUTHENTICATED_USER'] = $user;
    }

    public static function unregisterAuthenticatedUser( User $user )
    {
	unset( $_SESSION['AUTHENTICATED_USER'] );
    }
}

?>
