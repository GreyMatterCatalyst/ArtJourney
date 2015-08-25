<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'util/RequestParser' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );

class JourneyStepView extends View {
    const NUM_JOURNEY_STEPS = 6;
    const NUM_SIMILAR_CHOICES = 2;
    const GET_PARAM_NEXT_IMAGE_ID = 'nextImageId';
    const GET_PARAM_CHOSEN_ATTRIBUTE = 'chosenAttribute';
    
    
    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'JourneyStepView';
    }

    public function processRequest( User $user = NULL ) {
	// attempt to parse and load the next image id
	$nextImageId = RequestParser::parseRequestParam( $_REQUEST, self::GET_PARAM_NEXT_IMAGE_ID,
							 RequestParser::PARAM_FILTER_TYPE_INT );
	// if no image was specified, redirect to the start journey view
	$startJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'views/StartJourneyView' );
	if ( !$nextImageId ) {
	    header( "Location: $startJourneyUrl" );
	    exit;
	}
	    
	$dbConnection = DbConnectionUtil::getDbConnection( );
	$nextImageData = ImageData::loadImageDataById( $dbConnection, $nextImageId );
	if ( !$nextImageData ) {
	    header( "Location: $startJourneyUrl" );
	    exit;
	}

	parent::displayHeader( $user, 'Journey' );
       
	// attempt to parse the chosen attribute, if specified add to its tally
	if ( isset( $_GET[self::GET_PARAM_CHOSEN_ATTRIBUTE] ) ) {
	    $chosenAttribute =  $_GET[self::GET_PARAM_CHOSEN_ATTRIBUTE];
	    // verify the specified chosen attribute is actually an attribute of the chosen image
	    if ( $nextImageData->hasAttribute( $chosenAttribute ) ) {
		if ( !isset( $_SESSION['JOURNEY_ATTRIBUTE_MAP'][$chosenAttribute] ) )
		    $_SESSION['JOURNEY_ATTRIBUTE_MAP'][$chosenAttribute] = 1;
		else
		    $_SESSION['JOURNEY_ATTRIBUTE_MAP'][$chosenAttribute]++;
	    }
	}
	
	// store the current image id, in the journey session array, check for membership incase of a page re-load
	if ( !in_array( $nextImageId, $_SESSION['JOURNEY_IMAGE_LIST'] ) )
	    $_SESSION['JOURNEY_IMAGE_LIST'][] = $nextImageId;

	$commonAttributeImageDataTupleList = NULL;
	$commonAttributeImageIdList = NULL;
	$randomImageDataList = NULL;
	// find NUM_SIMILAR_CHOICES common images if possible
	$commonAttributeImageIdTupleList = ImageAttribute::loadCommonAttributeImageIdTupleList( $dbConnection, $nextImageId, $_SESSION['JOURNEY_IMAGE_LIST'] );
	//print( "<br/>common id list: " ); print_r( $commonAttributeImageIdTupleList ); print("<br/>" );
	shuffle( $commonAttributeImageIdTupleList );	    
	$commonAttributeImageIdList = Array( );	
	$commonAttributeImageDataTupleList = Array( );
	while( count( $commonAttributeImageDataTupleList ) < self::NUM_SIMILAR_CHOICES &&
	       count( $commonAttributeImageIdTupleList ) > 0 ) {
	    $currentEntry = Array( );
	    $commonAttributeImageIdTuple = array_shift( $commonAttributeImageIdTupleList );
	    if ( !in_array( $commonAttributeImageIdTuple['imageId'], $commonAttributeImageDataTupleList ) &&
		 !in_array( $commonAttributeImageIdTuple['imageId'], $_SESSION['JOURNEY_IMAGE_LIST'] ) ) {
		    $imageData = ImageData::loadImageDataById( $dbConnection, $commonAttributeImageIdTuple['imageId'] );
		    //print( "<br/>Image Data: " ); print_r( $imageData ); print( '<br/>' );
		    $commonAttributeImageIdList[] = $commonAttributeImageIdTuple['imageId'];
		    $currentEntry['imageData'] = $imageData;
		    $currentEntry['attribute'] = $commonAttributeImageIdTuple['attribute'];
		    $commonAttributeImageDataTupleList[] = $currentEntry;
	    }
	}

	//print( "<br/>common map: " ); print_r( $commonAttributeImageDataTupleList ); print("<br/>" );

	// add a random image to the choices list, and fill in the gaps if NUM_SIMILAR_CHOICES could not be found, if possible
	/*
	  print( "<br/>commonAttributeImageDataTupleList: " ); print_r( $commonAttributeImageDataTupleList ); print( "<br/>" );
	  print( "allImageIdList: " ); print_r( $allImageIdList ); print( "<br/>" );
	  print( "att_map: " ); print_r( $_SESSION['JOURNEY_IMAGE_LIST'] ); print( "<br/>" );
	*/
	$allImageIdList = ImageData::loadAllImageIdList( $dbConnection );
	shuffle( $allImageIdList );
	$randomImageIdList = Array( );
	while( ( ( count( $randomImageIdList ) + count( $commonAttributeImageIdList ) ) < ( self::NUM_SIMILAR_CHOICES + 1 ) ) && count( $allImageIdList ) > 0 ) {
	    $imageId = array_shift( $allImageIdList );
	    if ( $imageId != $nextImageId && !in_array( $imageId, $randomImageIdList ) && 
		 !in_array( $imageId, $commonAttributeImageIdList ) &&
		 !in_array( $imageId, $_SESSION['JOURNEY_IMAGE_LIST'] ) ) {
		$randomImageIdList[] = $imageId;
	    }
	}
	if ( !empty( $randomImageIdList ) )
	    $randomImageDataList = ImageData::loadImageDataListByIdSet( $dbConnection, $randomImageIdList );
	else
	    $randomImageDataList = Array( );


	// If the journey session array size has reached the number of journey steps, then set the is last
	// step flag to true, otherwise it should be false
	$IS_LAST_STEP = FALSE;
	if ( count( $_SESSION['JOURNEY_IMAGE_LIST'] ) == self::NUM_JOURNEY_STEPS )
	    $IS_LAST_STEP = TRUE;	    

	// display the current image
	?>
	<div class="currentJourneyImageSection">
	     <img src="<?=UrlFormatter::formatImageUrl( $nextImageData->getContentUri( ) )?>"/>
	</div>
	<?php

	// if this is not the last step, display further choices
        if ( !$IS_LAST_STEP ) {
	    ?>
	    <p class="imageGridHeader">Choose Your Journey's Next Step</p>
	    <div class="centerWrapper">
	    <div class="imageGrid">
	    <?php
	    // list common choices
	    foreach( $commonAttributeImageDataTupleList as $commonAttributeImageDataTuple) {
		$commonImageId = $commonAttributeImageDataTuple['imageData']->getId( );
		$commonImageThumbUrl = UrlFormatter::formatImageUrl( $commonAttributeImageDataTuple['imageData']->getThumbnailUri( ) );
		$commonAttribute = $commonAttributeImageDataTuple['attribute'];
		$choiceUrl = UrlFormatter::formatRoutingItemUrl( 'views/JourneyStepView', array( 
								     self::GET_PARAM_NEXT_IMAGE_ID => $commonImageId, 
								     self::GET_PARAM_CHOSEN_ATTRIBUTE => $commonAttribute ) );
		?>
		<a href="<?=$choiceUrl?>"><img src="<?=$commonImageThumbUrl?>"/></a>
		<?php
	    }
	    // list random choices 
	    foreach( $randomImageDataList as $randomImageData ) {
		$randomImageId = $randomImageData->getId( );
		$thumbnailUrl = UrlFormatter::formatImageUrl( $randomImageData->getThumbnailUri( ) );
		$choiceUrl = UrlFormatter::formatRoutingItemUrl( 'views/JourneyStepView', array(
								     self::GET_PARAM_NEXT_IMAGE_ID => $randomImageId ) );
		?>
		<a href="<?=$choiceUrl?>"><img src="<?=$thumbnailUrl?>"/></a>
		<?php
	    }
	    ?>
	    </div>
	    </div>
	    <?php
	    
	}
	// otherwise, display a button to finish the journey
	else {
	    $finishJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'views/FinishJourneyView' );
	    ?>
	    <div class="finishJourneyButtonSection">
		 <a class="button" href="<?=$finishJourneyUrl?>">Finish Journey</a>
	    </div>
	    <?php
	}

	parent::displayFooter( );
    }
}

?>