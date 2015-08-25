<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/Journey' );
ClassLoader::requireClassOnce( 'util/RequestParser' );

class SaveJourneyAction extends IndexRoutingItem {
    const POST_PARAM_JOURNEY_NAME = 'journey_name';
    const POST_PARAM_JOURNEY_COMMENTS = 'journey_comments';
    
    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'SaveJourneyAction';
    }

    public function processRequest( User $user = NULL ) {
	// check for a journey image list, if none exists, redirect to start journey view
	if ( !isset( $_SESSION['JOURNEY_IMAGE_LIST'] ) || empty( $_SESSION['JOURNEY_IMAGE_LIST'] ) ) {
	    $startJourneyUrl =  UrlFormatter::formatRoutingItemUrl( 'views/StartJourneyView' );
	    header( "Location: $startJourneyUrl" );
	    exit;
	}

	// check for journey name and comments, if they're missing, redirect to finish journey view
	$finishJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'views/FinishJourneyView' );
	if ( !isset( $_POST[self::POST_PARAM_JOURNEY_NAME] ) 
	     || empty( $_POST[self::POST_PARAM_JOURNEY_NAME] ) 
	     || !isset( $_POST[self::POST_PARAM_JOURNEY_COMMENTS] )
	     || empty( $_POST[self::POST_PARAM_JOURNEY_COMMENTS] ) ) {
	    header( "Location: $finishJourneyUrl" );
	    exit;
	}
	$journeyName = strip_tags( $_POST[self::POST_PARAM_JOURNEY_NAME] );
	$journeyName = str_replace("\\","", $journeyName);
	
	$journeyComments = strip_tags( $_POST[self::POST_PARAM_JOURNEY_COMMENTS] );
	$journeyComments = str_replace("\\","", $journeyComments);
		

	// populate and save a new journey data object
	$journeyData = new Journey( );
	$journeyData->setTitle( $journeyName );
	$journeyData->setComments( $journeyComments );
	$journeyData->setCreationDate( time( ) );
	
	foreach ( $_SESSION['JOURNEY_IMAGE_LIST'] as $imageDataId )
	    $journeyData->addImageId( $imageDataId );

	$dbConnection = DbConnectionUtil::getDbConnection( );
	$journeyData->save( $dbConnection );

	// unset the journey image list
	unset( $_SESSION['JOURNEY_IMAGE_LIST'] );
	unset( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] );

	// redirect to the journey details view
	$journeyDetailsUrl = UrlFormatter::formatRoutingItemUrl( 'views/JourneyDetailsView', array(
			     JourneyDetailsView::GET_PARAM_JOURNEY_ID => $journeyData->getId( ) ) );
	header( "Location: $journeyDetailsUrl" );	
    }
}

?>