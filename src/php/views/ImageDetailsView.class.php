<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'actions/EditImageAction' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );

/**
 * This class encapsulates the functionality of displaying the interface to view image data within the application.
 * @author craigb
 */
class ImageDetailsView extends View
{
    const GET_PARAM_RETURN_URL = 'return_url';
    const GET_PARAM_IMAGE_ID = 'image_id';

    /**
     * Constructs a new image details view.
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
	return 'ViewImage';
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
	parent::displayHeader( $user, 'Image Details' );

	if ( isset( $_REQUEST[ImageDetailsView::GET_PARAM_RETURN_URL] ) )
	{
	    ?>
	    <a href="<?=$_REQUEST[ImageDetailsView::GET_PARAM_RETURN_URL]?>">Return</a>
	    <br/>
	    <?php
	}

	if ( isset( $_REQUEST[ImageDetailsView::GET_PARAM_IMAGE_ID] ) )
	{
	    $imageId = $_REQUEST[ImageDetailsView::GET_PARAM_IMAGE_ID];
	    $imageData = ImageData::loadImageDataById( DbConnectionUtil::getDbConnection( ), $imageId );
	    if ( $imageData == NULL )
	    {
		print( "Failed to load image data " );
	    }
	    else
	    {
		$editImageUrl = UrlFormatter::formatRoutingItemUrl( 'views/EditImageView', array( EditImageView::GET_PARAM_IMAGE_ID => $imageData->getId( ) ) );
		$thumbnailUri = $imageData->getThumbnailUri( );
		$contentUri = $imageData->getContentUri( );
		?>
		<div class="imageDetailsView">
		<label for="title_field">Title</label>
                <br/>
	        <span id="title_field" class="imageDetailsViewField"><?=$imageData->getTitle( )?></span>
	        <br/>
                <label for="author_field">Author</label>
                <br/>
                <span id="author_field" class="imageDetailsViewField"><?=$imageData->getAuthor( )?></span>
                <br/>                
                <label for="year_field">Year</label>
                <br/>
                <span id="year_field" class="imageDetailsViewField"><?=$imageData->getYear( )?></span>
		<br/>
                <label for="attributes_field">Attributes</label>
                <br/>
                <table class="attribute_table" id="attributes_field">
                <?php
                foreach( $imageData->getAttributeList( ) as $attribute ) { 
                ?>
                   <tr><td><?=$attribute->getAttribute( )?></td></tr>
                <?php
                }
	        ?>
                </table>
	        <br/>
		<br/>
		<label for="content_uri_field">Image</label>
		<br/>
                <span id="content_uri_field" class="imageDetailsViewField">
                <image style="max-width: 100%" id="content_uri_field" src="<?=UrlFormatter::formatImageUrl( $contentUri )?>"/>
                </span>
    	        <br/><br/>
		<label for="thumbnail_field">Thumbnail</label>
                <br/>
		<img id="thumbnail_field" src="<?=UrlFormatter::formatImageUrl( $thumbnailUri )?>"/>
		<br/><br/>
		<?php if ( $user != NULL ) { ?>
		      <a class="button" href="<?=$editImageUrl?>">Edit</a>
		   <?php } ?>
	        </div>
	        <?php
	   }
	}
	else
	{
	    print( "No image specified" );
	}
	parent::displayFooter( );
    }
}


?>