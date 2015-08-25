<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/RequestParser' );
ClassLoader::requireClassOnce( 'model/Journey' );
ClassLoader::requireClassOnce( 'model/ImageData' );

class JourneyDetailsView extends View {
    const GET_PARAM_JOURNEY_ID = 'journey_id';

    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'JourneyDetailsView';
    }

    public function processRequest( User $user = NULL ) {
	$journeyId = RequestParser::parseRequestParam( $_GET, self::GET_PARAM_JOURNEY_ID, 	
					       RequestParser::PARAM_FILTER_TYPE_INT );
	$baseUrl = Settings::getSetting( 'APPLICATION_URL' );
	if ( !$journeyId ) {
	    header( "Location: $baseUrl" );
	    exit;
	}

	// load the journey data
	$dbConnection = DbConnectionUtil::getDbConnection( );
	$journeyData = Journey::loadJourneyById( $dbConnection, $journeyId );
	if ( !$journeyData ) {
	    header( "Location: $baseUrl" );
	    exit;
	}

	// load the images from the journey
	$imageDataList = ImageData::loadImageDataListByIdSet( $dbConnection, $journeyData->getImageIdList( ) );

	parent::displayHeader( $user, 'Journey Details' );
	?>
	<div class="imageDetailsView">
	     <label for="journey_name_field">Journey Name</label>
	     <br/>
             <span id="journey_name_field" class="imageDetailsViewField"><?=$journeyData->getTitle( )?></span>
	     <br/>
	     <label for="journey_creation_date_field">Journey Date</label>
             <br/>
             <span id="journey_creation_date_field" class="imageDetailsViewField"><?=$journeyData->getCreationDate( )?></span>
	     <br/>
             <label for="journey_comments_field">Journey Comments</label>
             <br/>
             <p id="journey_comments_field" class="imageDetailsViewField"><?=$journeyData->getComments( )?></p>
	</div>
        <div class="imageGrid">
        <?php 
            foreach( $imageDataList as $imageData ) {
	       $imageDetailsUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageDetailsView', array(
			      ImageDetailsView::GET_PARAM_IMAGE_ID => $imageData->getId( ) ) );
	       $thumbnailUrl = UrlFormatter::formatImageUrl( $imageData->getThumbnailUri( ) );
	       ?>
	       <a target="_blank" href="<?=$imageDetailsUrl?>"><img src="<?=$thumbnailUrl?>"/></a>
	       <?php
	    }
        ?>
        </div>
	
	<?php
	parent::displayFooter( );
    }
}


?>