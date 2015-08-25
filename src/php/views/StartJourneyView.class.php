<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'views/JourneyStepView' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );
ClassLoader::requireClassOnce( 'model/Journey' );

class StartJourneyView extends View {
    const NUM_START_SELECTION_COUNT = 6;    

    public function __construct( )  {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'StartJourneyView';
    }

    public function processRequest( User $user = NULL ) {
	parent::displayHeader( $user, 'Start Journey' );

	// TODO query for existing journeys

	// query for distinct image attributes
	$dbConnection = DbConnectionUtil::getDbConnection( );
	$distinctAttributeList = ImageAttribute::loadExistingValues( $dbConnection );
	// randomly select attributes, foreach attribute, retrieve the id of a unique image, store in sourceImageList
	shuffle( $distinctAttributeList );
	$sourceImageIdList = array( );
	for( $i = 0; $i < self::NUM_START_SELECTION_COUNT && count( $distinctAttributeList ) > 0; $i++ ) {
	    $attribute = array_shift( $distinctAttributeList );
	    $imageIdList = ImageAttribute::loadImageIdListByAttribute( $dbConnection, $attribute );
	    shuffle( $imageIdList );
	    
	    while( count( $imageIdList ) > 0 ) {
		$imageId = array_shift( $imageIdList );
		if ( !in_array( $imageId, $sourceImageIdList ) ) {
		    $sourceImageIdList[] = $imageId;
		    break;
		}
	    }
	}

	// if the full number of starting images could not be found with distinct attributes, then randomly select more to fill in the gap
	if ( count( $sourceImageIdList ) < self::NUM_START_SELECTION_COUNT ) {
	    $allImageIdList = ImageData::loadAllImageIdList( $dbConnection );
	    shuffle( $allImageIdList );
	    while( count( $sourceImageIdList ) < self::NUM_START_SELECTION_COUNT &&
		   count( $allImageIdList ) > 0 ) {
		$imageId = array_shift( $allImageIdList );
		if ( !in_array( $imageId, $sourceImageIdList ) )
		    $sourceImageIdList[] = $imageId;
	    }
	}
	
	// load the randomly selected images
	$sourceImageDataList = ImageData::loadImageDataListByIdSet( $dbConnection, $sourceImageIdList );
	shuffle( $sourceImageDataList );

	// reset the journey's session data
	if ( isset( $_SESSION['JOURNEY_IMAGE_LIST'] ) ) {
	    unset( $_SESSION['JOURNEY_IMAGE_LIST'] );
	}
	$_SESSION['JOURNEY_IMAGE_LIST'] = Array( );	
	if ( isset( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] ) ) {
	    unset( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] );
	}
	$_SESSION['JOURNEY_ATTRIBUTE_MAP'] = Array( );

	?>
	<p class="imageGridHeader">Choose Your Journey's Starting Image</p>
	<div class="centerWrapper">
	<div class="imageGrid">	     
             <?php
             foreach( $sourceImageDataList as $imageData ) {
	    $imageJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'views/JourneyStepView', array(
								       JourneyStepView::GET_PARAM_NEXT_IMAGE_ID => $imageData->getId( ) ) );
		 $imageThumbUrl = UrlFormatter::formatImageUrl( $imageData->getThumbnailUri( ) );
		 ?>
		 <a href="<?=$imageJourneyUrl?>"> <img src="<?=$imageThumbUrl?>"></a>
		 <?php
	     }
             ?>
	</div>
	</div>
	<?php

	      // TODO display a list of existing journeys

	parent::displayFooter( );
    }
}
?>