<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'views/ImageSearchResultsView' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'model/UserRole' );

/**
 * This class encapsulates the functionality of displaying the image search view to the requester.
 * @author craigb
 */
class ImageSearchView extends View
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
	return 'ImageSearch';
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
	parent::displayHeader( $user, 'Image Search' );
	$searchActionUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageSearchResultsView' );

	?>
	<form class="imageForm" method="POST" action="<?=$searchActionUrl?>">
	     <label for="title_field">Title</label>
             <br/>
             <input type="text" id="title_field" name="<?=ImageSearchresultsView::POST_PARAM_TITLE?>"/>
             <?php parent::formatImageDataAutoComplete( DbConnectionUtil::getDbConnection( ), 'title', 'title_field' );?>
	     <br/>
             <label for="author_field">Author</label>
	     <br/>
             <input type="text" id="author_field" name="<?=ImageSearchResultsView::POST_PARAM_AUTHOR?>"/>
             <?php parent::formatImageDataAutoComplete( DbConnectionUtil::getDbConnection( ), 'author', 'author_field' );?>
	     <br/>
             <label for="year_field">Year</label>
             <br/>
             <input id="year_field" name="<?=ImageSearchResultsView::POST_PARAM_YEAR?>"/>
             <?php parent::formatImageDataAutoComplete( DbConnectionUtil::getDbConnection( ), 'year', 'year_field' ); ?>
             <br/>
             <label for="attribute_field">Attribute</label>
             <br/>
             <input id="attribute_field" name="<?=ImageSearchResultsView::POST_PARAM_ATTRIBUTE?>"/>
             <?php parent::formatImageAttributeDataAutoComplete( DbConnectionUtil::getDbConnection( ), 'attribute_field' );?>
	     <br/>
             <input type="submit" class="button" value="Search">
	</form>
	<?php
	parent::displayFooter( );
    }
}

?>