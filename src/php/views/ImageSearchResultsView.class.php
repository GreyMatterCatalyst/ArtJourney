<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'model/UserRole' );
ClassLoader::requireClassOnce( 'util/RequestParser' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/ImageData' );

class ImageSearchResultsView extends View {
    const POST_PARAM_TITLE = 'title';
    const POST_PARAM_AUTHOR = 'author';
    const POST_PARAM_YEAR = 'year';
    const POST_PARAM_ATTRIBUTE = 'attribute';

    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'ImageSearchResults';
    }

    public function processRequest( User $user = NULL ) {	
	parent::displayHeader( $user, 'Image Search Results' );

	// compose and execute query
	$dbConnection = DbConnectionUtil::getDbConnection( );
	$queryString = 'SELECT imd.id FROM image_data imd ';

	$attribute = RequestParser::parseRequestParam( $_REQUEST, ImageSearchResultsView::POST_PARAM_ATTRIBUTE, RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC_WS_ONLY );
	if ( $attribute != NULL ) {
	    $queryString .= "INNER JOIN image_attribute ima ON imd.id = ima.image_data_id AND ima.attribute LIKE '%$attribute%' ";
	}

	$queryString .= ' WHERE 1=1 ';

	$title = RequestParser::parseRequestParam( $_REQUEST, ImageSearchResultsView::POST_PARAM_TITLE, RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC_WS_ONLY );
	if ( $title != NULL )
	    $queryString .= "AND imd.title LIKE '%$title%' ";

	$year = RequestParser::parseRequestParam( $_REQUEST, ImageSearchResultsView::POST_PARAM_YEAR, RequestParser::PARAM_FILTER_TYPE_INT );
	if ( $year != NULL )
	    $queryString .= "AND imd.year LIKE '%$year%' ";	

	$author = RequestParser::parseRequestParam( $_REQUEST, ImageSearchResultsView::POST_PARAM_AUTHOR, RequestParser::PARAM_FILTER_TYPE_ALPHA_WS_ONLY );
	if ( $author != NULL )
	    $queryString .= "AND imd.author LIKE '%$author%' ";


	//print( "<br/>queryString: $queryString<br/><br/>" );
	$preparedStatement = $dbConnection->prepare( $queryString );
	$preparedStatement->execute( );
	$idList = array( );
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $idList[] = $resultRow['id'];
	$preparedStatement = NULL;

	if ( count ( $idList ) > 0 )
		$imageDataResultList = ImageData::loadImageDataListByIdSet( $dbConnection, $idList );
   else 	
      $imageDataResultList = array( );
	
		?>
	<h1>Search Results</h1>
	<br/>
	<div class="imageDetailsView">
	<?php

	     foreach( $imageDataResultList as $imageData ) {
		 $viewImageDetailsUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageDetailsView', array(
			ImageDetailsView::GET_PARAM_IMAGE_ID => $imageData->getId( ) ) );
		 ?>
		 <div class="searchResultItem">
		 <?php
		 $thumbnailUri = $imageData->getThumbnailUri( );
		 if ( !empty( $thumbnailUri ) ) {
		       ?>
		       <div>
		       	<a href="<?=$viewImageDetailsUrl?>"><img  id="thumbnail_field" src="<?=UrlFormatter::formatImageUrl( $imageData->getThumbnailUri( ) )?>"/></a>
			 <br/>
			 <a href="<?=$viewImageDetailsUrl?>">Details</a>
		       
		       </div>
		       <?php
		       	$title = $imageData->getTitle( );
		       	if ( $title != null ) {
		       		?>
		       		<label for="title_field">Title</label>
		      		 <span  id="title_field" class="imageDetailsField"><?=$title?></span>
		      		 <br/>
		      		 <?php
		       	}
		       ?>
		       <?php
		       	$author = $imageData->getAuthor( );
		       	if ( $author != null ) {
		       		?>
		       		<label for="author_field">Author</label>
		      		 <span  id="author_field" class="imageDetailsField"><?=$author?></span>
		      		 <br/>
		      		 <?php
		       	}
		       ?>
			<br/>
		       	
		       <?php
		 }
	         ?>
		 </div>
		 <div style="clear:both"></div>
		 <?php
		 
	     }

	?>
	</div>
	<?php

	parent::displayFooter( );
    }
}
?>