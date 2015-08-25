<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'util/RequestParser' );

/**
 * This class encapsulates the functionality of accessing javascript resources for this application.
 * This provides the flexibility of requiring authentication for specified scripts if it becomes necessary.
 * @author craigb
 */
class AccessJavascriptAction extends IndexRoutingItem
{
    // This constant defines the get parameter for specifying the javascript file to access
    const JAVASCRIPT_FILE_GET_PARAM = 'js_file';

    private $baseJavascriptDir;

    /**
     * Constructs a new access javascript action object.
     */
    public function __construct( )
    {
	parent::__construct( FALSE );
	$this->baseJavascriptDir = dirname( dirname( __FILE__ ) ) . '/js/';
    }

    /**
     * This function returns the index routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'AccessJavascript';
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
	// if for some reason we decide to require authentication for some scripts, this would be the place to put such functionality

	$requestedFileName = RequestParser::parseRequestParam( $_GET, AccessJavascriptAction::JAVASCRIPT_FILE_GET_PARAM );
	// retrieve the requested javascript file from the request
	if ( $requestedFileName != NULL )
	{
	    $filePath = $this->baseJavascriptDir . $requestedFileName;
	    // verify the file exists and is readable
	    if ( file_exists( $filePath ) && is_readable( $filePath ) )
	    {
		// deliver the javascript file
		header( 'Content-type: text/javascript' );
		readfile( $filePath );
	    }
	}
    }
}
