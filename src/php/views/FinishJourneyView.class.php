<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'actions/SaveJourneyAction' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );

class FinishJourneyView extends View {
    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'FinishJourneyView';
    }

    private function displayImageDetailLink( ImageData $imageData ) {
	$imageDetailsUrl = UrlFormatter::formatRoutingItemUrl( 'views/ImageDetailsView', array(
			      ImageDetailsView::GET_PARAM_IMAGE_ID => $imageData->getId( ) ) );
	$thumbnailUrl = UrlFormatter::formatImageUrl( $imageData->getThumbnailUri( ) );
	?>
	<a target="_blank" href="<?=$imageDetailsUrl?>"><img src="<?=$thumbnailUrl?>"/></a>
	<?php
    }

    public function processRequest( User $user = NULL ) {
	// if no journey exists, then redirect to the start journey page
	$startJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'views/StartJourneyView' );
	if ( !isset( $_SESSION['JOURNEY_IMAGE_LIST'] ) || empty( $_SESSION['JOURNEY_IMAGE_LIST'] ) ) {
	    header( "Location: $startJourneyUrl" );
	    exit;
	}

	parent::displayHeader( $user, 'Finish Journey' );

	//print( "JOURNEY IMAGE LIST: " ); print_r( $_SESSION['JOURNEY_IMAGE_LIST'] ); print( "<br/>" );
	// load the image data for images encountered on the journey
	$dbConnection = DbConnectionUtil::getDbConnection( );
	$imageDataList = NULL;
	if ( !empty( $_SESSION['JOURNEY_IMAGE_LIST'] ) )
	    $imageDataList = ImageData::loadImageDataListByIdSet( $dbConnection, $_SESSION['JOURNEY_IMAGE_LIST'] );
	//print( "JOURNEY ATTRIBUTE MAP: " ); print_r( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] ); print( "<br/>" );
	// if possible, load image data for images with common attributes
	$commonImageDataList = NULL;
	$commonAttribute = NULL;
	//print( "<br/>att map: " ); print_r( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] ); print("<br/>" );
	if ( isset( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] ) && !empty( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] ) ) {
	    // find the attribute with the maximum number of entries
	    $maxAttribute = NULL;
	    $maxAttributeCount = 0;
	    foreach( $_SESSION['JOURNEY_ATTRIBUTE_MAP'] as $attribute => $count ) {
		if ( $maxAttribute == NULL || $maxAttributeCount < $count ) {
		    $maxAttribute = $attribute;
		    $maxAttributeCount = $count;
		}
	    }
	    $commonAttribute = $maxAttribute;

	    // find images with common attributes
	    $tempCommonImageIdList = ImageAttribute::loadCommonAttributeImageIdList( $dbConnection, $commonAttribute, $_SESSION['JOURNEY_IMAGE_LIST'] );
	    $commonImageIdList = Array( );
	    foreach( $tempCommonImageIdList as $imageId ) {
		if ( !in_array( $imageId, $_SESSION['JOURNEY_IMAGE_LIST'] ) )
		    $commonImageIdList[] = $imageId;
	    }
	    if ( !empty( $commonImageIdList ) )
		$commonImageDataList = ImageData::loadImageDataListByIdSet( $dbConnection, $commonImageIdList );
	    
	}

	// display the journey in an image grid
	?>
	<p class="imageGridHeader">Your Journey Through Art</p>
	<div class="centerWrapper">
        <div class="imageGrid">
	<?php
	foreach ( $imageDataList as $imageData )
	     $this->displayImageDetailLink( $imageData );
	?>
	</div>
	</div>
	<?php
	if ( $commonImageDataList != NULL ) {
	    ?>
	    <p class="imageGridHeader">Suggested Art</p>
	    <div class="centerWrapper">
		<p>Based upon your choices here is a selection of other art pieces to examine</p>
	    <div class="imageGrid">
	    <?php
	    foreach ( $commonImageDataList as $imageData )
		$this->displayImageDetailLink( $imageData );
	    ?>
	    </div>
	    </div>
	    <?php
	}
	$saveJourneyUrl = UrlFormatter::formatRoutingItemUrl( 'actions/SaveJourneyAction' );
	?>
	<script type="text/javascript">
	function validate( ) {
	    var result = true;
	    $("#errorSection").html("");
	    var nameVal = $("#journey_name_field").val( );
	    if( nameVal == null || nameVal == "" ) {
		$("#errorSection").append( "<span clas=\"errorMessage\">Journey Name Required</span><br/>" );
		$("#errorSection").css( "display", "inline-block");
		$("#journey_name_field_label").css( "color", "#EE3124" );
		result = false;
	    }
	    var commentsVal = $("#journey_comments_field").val( );
	    if( commentsVal == null || commentsVal == "" ) {
		$("#errorSection").append( "<span clas=\"errorMessage\">Comments Required</span><br/>" );
		$("#errorSection").css( "display", "inline-block");
		$("#journey_comments_field_label").css( "color", "#EE3124" );
		result = false;
	    }
	    return result;
	}
	</script>
	<p class="imageGridHeader">Journey Feedback</p>
        <p style="width:50%">
	     You are now encouraged to take a moment to reflect on your journey through art. Consider the feelings which were illicited during the experience. Also think about how each piece might have had an affect on your perception of subsequent pieces. Please share your thoughts in the comments field.
	</p>
	<form class="imageForm" method="POST" action="<?=$saveJourneyUrl?>" onsubmit="return validate( )">
	     <div class="errorSection" id="errorSection"></div>
	     <br/>
	     <label id="journey_name_field_label" for="journey_name_field">Journey Name</label>
	     <br/>
             <input id="journey_name_field" type="text" name="<?=SaveJourneyAction::POST_PARAM_JOURNEY_NAME?>"/>
             <br/>
             <label id="journey_comments_field_label" for="journey_comments_field">Comments</label>
             <br/>
             <textarea id="journey_comments_field" rows="10" cols="100" name="<?=SaveJourneyAction::POST_PARAM_JOURNEY_COMMENTS?>" value=""></textarea>
             <br/>
             <input class="button" type="submit" value="Save"/>
	</form>
	<?php
	      
	parent::displayFooter( );
    }
}
?>