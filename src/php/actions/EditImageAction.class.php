<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );
ClassLoader::requireClassOnce( 'views/ImageDetailsView' );
ClassLoader::requireClassOnce( 'util/RequestParser' );

/**
 * This class implements an action which provides image editing functionality.
 * @author craigb
 */
class EditImageAction extends IndexRoutingItem
{
    // This constant defines the get parameter for image id
    const GET_PARAM_IMAGE_ID = 'image_id';
    const POST_PARAM_AUTHOR = 'author';
    const POST_PARAM_TITLE = 'title';
    const POST_PARAM_YEAR = 'year';    
    const POST_PARAM_THUMBNAIL = 'thumbnail';
    const POST_PARAM_FILE = 'file';
    const POST_PARAM_ATTRIBUTE_LIST = 'attributeList';

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
	return 'EditImageAction';
    }

    private function processImageUpload( $directory, $imageId, $paramName ) {
	$extension = end( explode( ".", $_FILES[$paramName]['name'] ) );
	$imageRelPath = "$directory/$imageId.$extension";
	$imageFilePath = dirname( dirname( __FILE__ ) ) . '/images/' . $imageRelPath;
	move_uploaded_file( $_FILES[$paramName]['tmp_name'], $imageFilePath );
	return $imageRelPath;
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
	// if image id was specified, parse it, attempt to load an image data object using the id
	$imageId = RequestParser::parseRequestParam( $_REQUEST, EditImageAction::GET_PARAM_IMAGE_ID, RequestParser::PARAM_FILTER_TYPE_INT );
	$imageData = NULL;
	if ( $imageId != NULL )
	    $imageData = ImageData::loadImageDataById( DbConnectionUtil::getDbConnection( ), $imageId );

	// otherwise create a new image data object
	if ( $imageData == NULL )
	{
	    $imageData = new ImageData( );
	    $imageData->setSubmitterUserId( $user->getId( ) );
	}

	// set the fields in the image data object
	$author = NULL;
	if ( isset( $_POST['author'] ) )
	    $author = $_POST['author'];
	if ( $author != NULL )
	    $imageData->setAuthor( $author );
	$title = NULL;
	if ( isset( $_POST['title'] ) )
	    $title = $_POST['title'];
	if( $title != NULL )
	    $imageData->setTitle( $title );
	$year = NULL;
	if ( isset( $_POST['year'] ) )
	    $year = $_POST['year'];
	if ( $year != NULL )
	    $imageData->setYear( $year );

	// update attributes list
	$attributeString = $_POST[EditImageAction::POST_PARAM_ATTRIBUTE_LIST];
	if ( !empty( $attributeString ) )
	    $attributeStringList = explode( ",", $attributeString );
	else
	    $attributeStringList = Array( );
	    
	// add new attributes
	foreach( $attributeStringList as $attString ) {
	    if ( $attString != "" ) {
		if ( !$imageData->hasAttribute( $attString ) ) 
		    $imageData->addAttributeByString( $attString );
	    }
	}

	$dbConnection = DbConnectionUtil::getDbConnection( );
	// remove deleted ones
	$attributeList = $imageData->getAttributeList( );
	foreach( $attributeList as $attribute ) {
	    if ( !in_array( $attribute->getAttribute( ), $attributeStringList ) ) {
		$imageData->removeAttributeBystring( $attribute->getAttribute( ) );
		$attribute->delete( $dbConnection );
	    }
	}

	// save the image data object
	$imageData->save( $dbConnection );

	// if image data was uploaded, process the image uploads and save again	
	if ( !empty( $_FILES[EditImageAction::POST_PARAM_THUMBNAIL]['name'] ) )
	{
	    $thumbnailUri = $this->processImageUpload( 'image_data_thumbs', $imageData->getId( ), EditImageAction::POST_PARAM_THUMBNAIL );
	    $imageData->setThumbnailUri( $thumbnailUri );
	    $imageData->save( $dbConnection );
	}
	if( !empty( $_FILES[EditImageAction::POST_PARAM_FILE]['name'] ) ) {
	    $fileUri = $this->processImageUpload( 'image_data', $imageData->getId( ), EditImageAction::POST_PARAM_FILE );
	    $imageData->setContentUri( $fileUri );
	    $imageData->save( $dbConnection );
	}
	

	// redirect the user to the image viewing page
	$getParamMap = array( ImageDetailsView::GET_PARAM_IMAGE_ID => $imageData->getId( ) );
	$imageDetailsViewUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageDetailsView', $getParamMap );
	header( "Location: $imageDetailsViewUrl" );
    }
}