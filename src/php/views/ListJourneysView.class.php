<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'views/JourneyDetailsView' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'util/Settings' );
ClassLoader::requireClassOnce( 'util/RequestParser' );
ClassLoader::requireClassOnce( 'model/Journey' );
ClassLoader::requireClassOnce( 'model/ImageData' );

class ListJourneysView extends View {
    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'ListJourneysView';
    }

    public function processRequest( User $user = NULL ) {
	parent::displayHeader( $user, 'Journeys List' );

	$dbConnection = DbConnectionUtil::getDbConnection( );
	$journeyIdList = Journey::loadAllJourneyIdList( $dbConnection );
	$journeyDataList = Array( );
	foreach( $journeyIdList as $journeyId )
	    $journeyDataList[] = Journey::loadJourneyById( $dbConnection, $journeyId );

	?>
        <div class="imageDetailsView">
        <?php
	    foreach( $journeyDataList as $journeyData ) {
	    $journeyDetailUrl = UrlFormatter::formatRoutingItemUrl( 'views/JourneyDetailsView', array( JourneyDetailsView::GET_PARAM_JOURNEY_ID => $journeyData->getId( ) ) );
	    ?>
	    <div style="display:inline-block" class="searchResultItem">
	    <label for="journey_name_field">Journey Name</label>
	    <br/>
	    <a href="<?=$journeyDetailUrl?>"><span id="journey_name_field" class="imageDetailsField"><?=$journeyData->getTitle( )?></span></a>
	    <br/>
	    <label for="journey_date_field">Journey Date</label>
	    <br/>
	    <span id="journey_date_field" class="imageDetailsField"><?=$journeyData->getCreationDate( )?></span>
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