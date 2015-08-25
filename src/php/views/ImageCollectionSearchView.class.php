<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );

/**
 * This class encapsulates the functionality of displaying the image collection search view to the requester.
 * @author craigb
 */
class ImageCollectionSearchView extends View
{
    /**
     * Constructs a new landing page view.
     */
    public function __construct( )
    {
	parent::__construct( FALSE );
    }
    
    /**
     * This function returns the routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'ImageCollectionSearch';
    }

    /**
     * This function performs the processing of the user request.
     * @param User $user The requesting user. If the user is not null, then by convention
     * actions will assume the user is authenticated, otherwise not.
     * @throws Exception If an error was encountered while processing this request, an exception
     * will be thrown.
     * @return void
     */
    public function processRequest( User $user = NULL )
    {
	parent::displayHeader( $user, 'Image Collection Search' );
	// TODO finish me
	parent::displayFooter( );
    }
}

?>