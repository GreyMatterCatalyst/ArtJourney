<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'util/RequestParser' );

/**
 * This class processes image access requests. This is necessary as research images need to be
 * restricted to authenticated users.
 * @author craigb
 */
class AccessImageAction extends IndexRoutingItem
{
    // A constant defining the GET param for relative image paths
    const RELATIVE_IMAGE_PATH_GET_PARAM = 'rel_image_path';
    // A constant defining the relative path to the default error image
    const ERROR_IMAGE_PATH = 'ui/error_image.png';

    // this maps image directories to whether or not they require the user to be authenticated.
    private $imageDirectoryAuthorizationMap;
    private $baseImageDir;

    /**
     * Constructs a new AccessImageAction object.
     */
    public function __construct( )
    {
	parent::__construct( FALSE );
	$this->baseImageDir = dirname( dirname( __FILE__ ) ) . '/images/';
	$this->imageDirectoryAuthorizationMap = array( );
	$this->imageDirectoryAuthorizationMap[$this->baseImageDir . 'ui'] = FALSE;
	$this->imageDirectoryAuthorizationMap[$this->baseImageDir .'image_data_thumbs'] = FALSE;
	$this->imageDirectoryAuthorizationMap[$this->baseImageDir .'image_data'] = FALSE;
	
    }

    /**
     * This abstract function returns the index routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'AccessImage';
    }

    /**
     * This function performs the processing of the user request.
     * @param User $user The requesting user, only if this action requires authorization, 
     * default to NULL otherwise.
     * @throws Exception If an error was encountered while processing this request, an exception
     * will be thrown.
     * @return void
     */
    public function processRequest( User $user = NULL)
    {
	$relativeImagePath = RequestParser::parseRequestParam( $_GET, AccessImageAction::RELATIVE_IMAGE_PATH_GET_PARAM );
	// if a relative image was specified as a request parameter, process the request
	if ( $relativeImagePath != NULL )
	{
	    $imagePath = $this->baseImageDir . $relativeImagePath;
	    // ensure the specified path exists and is readable
	    if( file_exists( $imagePath ) && is_readable( $imagePath ) )
	    {
		// parse the directory of the requested image
		$directoryPath = dirname( $imagePath );
		if ( !empty( $directoryPath ) )
		{
		    // check to see if the directory requires authentication, if so verify the user is authenticated
		    if ( isset( $this->imageDirectoryAuthorizationMap[$directoryPath] ) &&
			 $this->imageDirectoryAuthorizationMap[$directoryPath] )
		    {
			// if the user is null, then the requester is not authenticated
			if ( $user == NULL )
			{
			    $this->displayDefaultErrorImage( );
			    return;
			}
		    }

		    // at this point it is assumed the requester is allowed to view the image
		    $this->displayImage( $imagePath );
		    return;
		}
	    }
	}
	
	// otherwise display an error image
	$this->displayDefaultErrorImage( );
    }

    /**
     * Displays the default error image to the requester.
     * @return void
     */
    private function displayDefaultErrorImage( )
    {
	$this->displayImage( $this->baseImageDir . AccessImageAction::ERROR_IMAGE_PATH );
    }

    /**
     * This function displays properly formats the header information for the specified image, and then
     * displays the image to the requester.
     * @param String $imagePath The path of the image to be displayed.
     * @return void
     */
    private function displayImage( $imagePath )
    {
	// parse the file type to properly send the header information to the browser
	$tokens = explode( '.', $imagePath );
	// if a file type could not be parsed, then display the error image
	if ( count( $tokens ) == 0 || !isset( $tokens[count( $tokens ) -1] ) )
	{
	    $this->displayDefaultErrorImage( );
	    return;
	}

	// otherwise parse the type, send the properly formatted header, and read the image file
	$imageFileType = $tokens[count( $tokens )-1];
	header( "Content-Type: image/$imageFileType" );
	readfile( $imagePath );
    }
}

?>
