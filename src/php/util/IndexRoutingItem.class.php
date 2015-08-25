<?php
ClassLoader::requireClassOnce( 'model/User' );

/**
 * This abstract class encapsulates the functionality of index routing items for this application.
 * Routing items are views/actions which get routed to by the index controller.
 * @author craigb
 */
abstract class IndexRoutingItem
{
    // This constant defines the get parameter for index map items
    const INDEX_ROUTING_ITEM_GET_PARAM = 'action';

    // This constant defines the key prefix for routing arguments.
    const ROUTING_SESSION_KEY_PREFIX = 'routing_argument_';

    private $requiresAuthentication;
    private $requiredRoleTypeList;

    /**
     * Constructs a new index routing item object.
     * @param boolean $requiresAuthentication Whether or not this action requires an authenticated user.
     * @param array $requiredRoleTypeList If user authentication is required, this list defines the
     * roles which are required for user authorization. Defaults to NULL.
     */
    public function __construct( $requiresAuthentication, array $requiredRoleTypeList = NULL )
    {
	$this->requiresAuthentication = $requiresAuthentication;
	$this->requiredRoleTypeList = $requiredRoleTypeList;
    }

    /**
     * Returns whether or not this routing item requires user authentication for access.
     * @return boolean Whether or not this action requires user authentication for access.
     */
    public function requiresAuthentication( )
    {
	return $this->requiresAuthentication;
    }

    /**
     * Checks whether or not the specified user meets the role requirements to be authorized
     * for this routing item.
     * @param User $user The user being checked for authorization.
     * @return booelan Whether or not the specified user meets the role requirements to be authorized
     * for this routing item.
     */
    public function isUserAuthorized( User $user )
    {
	return TRUE;
    }

    /**
     * This function provides a mechanism for passing an argument to another routing item.
     * @param String $argumentKey The unique key for the argument being passed.
     * @param mixed $argumentValue The value of the argument being passed.
     * @return void
     */
    protected function passRoutingArgument( $argumentKey, $argumentValue )
    {
	$_SESSION[IndexRoutingItem::ROUTING_SESSION_KEY_PREFIX . $argumentKey] = $argumentValue;
    }

    /**
     * This function provides a mechanism for receiving arguments from another index routing item.
     * Upon successful retrieval of the argument it is removed from the session.
     * @param String $argumentKey The key of the argument to be extracted.
     * @return mixed The extracted argument object if found, otherwise NULL.
     */
    protected function extractRoutingArgument( $argumentKey )
    {
	$sessionKey = IndexRoutingItem::ROUTING_SESSION_KEY_PREFIX . $argumentKey;
	if ( isset( $_SESSION[$sessionKey] ) )
	{
	    $argument = $_SESSION[$sessionKey];
	    unset( $_SESSION[$sessionKey] );
	    return $argument;
	}
	return NULL;
    }

    /**
     * This abstract function returns the routing key for the implementing class.
     * @return String The index routing key for the implementing class.
     */
    abstract public static function getRoutingKey( );

    /**
     * This abstract function performs the processing of the user request.
     * @param User $user The requesting user. If the user is not null, then by convention
     * actions will assume the user is authenticated, otherwise not.
     * @throws Exception If an error was encountered while processing this request, an exception
     * will be thrown.
     * @return void
     */
    abstract public function processRequest( User $user = NULL );
}
?>
