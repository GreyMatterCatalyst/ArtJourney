<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );
ClassLoader::requireClassOnce( 'actions/EditImageAction' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );

/**
 * This class encapsulates the functionality of displaying the interface to edit image data within the application.
 * @author craigb
 */
class EditImageView extends View
{
    const GET_PARAM_IMAGE_ID = 'image_id';

    /**
     * Constructs a new landing page view.
     */
    public function __construct( )
    {
	parent::__construct( TRUE, array( UserRole::USER_ROLE_TYPE_EDITOR ) );
    }
    
    /**
     * This function returns the routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'EditImage';
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
	// if an image id was specified, assuming editting an existing image, otherwise assume it's a new image
	$imageId = NULL;
	if ( isset( $_GET[EditImageView::GET_PARAM_IMAGE_ID] ) )
	    $imageId = $_GET[EditImageView::GET_PARAM_IMAGE_ID];
	$pageHeader = $imageId == NULL ? 'Add New Image' : 'Edit Image';
	parent::displayHeader( $user, $pageHeader );
	$editImageActionUrl = NULL;
	$getParamMap = NULL;
	$imageData = NULL;
	if ( $imageId != NULL )
	{
	    $getParamMap = array( );
	    $getParamMap[EditImageAction::GET_PARAM_IMAGE_ID] = $imageId;
	    $imageData = ImageData::loadImageDataById( DbConnectionUtil::getDbConnection( ), $imageId );
	}
	
	$author = '';
	$title = '';
	$year = '';
	$attributeList = NULL;
	$attributeListString = "";
	$thumbnailUri = NULL;
	$contentUri = NULL;
	if ( $imageData != NULL ) 
	{
	    $contentUri = $imageData->getContentUri( );
	    $thumbnailUri = $imageData->getThumbnailUri( );
	    $title = $imageData->getTitle( );
	    $year = $imageData->getYear( );
	    $author = $imageData->getAuthor( );
	    $attributeList = $imageData->getAttributeList( );
	    foreach( $attributeList as $attribute )
		$attributeListString .= "," . $attribute->getAttribute( );
	}

	$editImageActionUrl = UrlFormatter::formatRoutingItemUrl( 'actions/EditImageAction', $getParamMap );
	$submitLabel = $imageId == NULL ? 'Create' : 'Save';
	?>
	<script type="text/javascript" >
	
	function replaceThumbnail( ) {
		$("#thumbnail_div").html( "<input type=\"file\" id=\"thumbnail_field\" name=\"<?=EditImageAction::POST_PARAM_THUMBNAIL?>\">" ); 
	}

	function replaceFile( ) {
		$("#file_div").html( "<input type=\"file\" id=\"file_field\" name=\"<?=EditImageAction::POST_PARAM_FILE?>\">" ); 
	}

	function cancel( ) {
	    var previousPageUrl = "<?=$_SERVER['HTTP_REFERER']?>";
	    window.location.href = previousPageUrl;
	}

	function removeAttributeAtIndex( index ) {
	    var rowList = $("#attribute_list_table tr");
	    
	    for( i = 0; i < rowList.length; i++ ) {
		if ( i == index ) {
		    var removedAttString = $( rowList[i] ).children( ).first( ).text( );
		    var attList = $( "#attribute_list").val( ).split( "," );
		    attList.splice( attList.indexOf( removedAttString ), 1 );
		    $( "#attribute_list").val( attList.join( ) );
		    $( rowList[i] ).remove( );		    
		}
	    }
	}

	function addAttribute( ) {
	    var attributeValue = prompt( "Specify New Attribute" );
	    var attList = $( "#attribute_list").val( ).split( "," );
	    if ( attributeValue != null && attributeValue != "" && attList.indexOf( attributeValue ) == -1 ) {
		var newIndex = $( "#attribute_list_table tr" ).length;
		$( "#attribute_list_table" ).append( "<tr><td>" + attributeValue + "</td><td><a onclick=\"removeAttributeAtIndex( " + newIndex + " );\">Remove</a></td></tr>" );
		
		attList.push( attributeValue );
		$( "#attribute_list").val( attList.join( ) );
	    }
	}
	
	function validate( ) {
	    var result = true;
	    $("#errorSection").html("" );
	    <?php
	    if ( $imageData == NULL ) {
		?>
		var thumbnailVal = $("#thumbnail_field").val( );
		if ( thumbnailVal == null || thumbnailVal == "" ) {
		    $("#errorSection").append( "<span class=\"errorMessage\">Thumbnail Required</span><br/>" );
		    $("#errorSection").css( "display", "inline-block");
		    $("#thumbnail_field_label").css( "color", " #EE3124" );
		    result = false;
		}
		var fileVal = $("#file_field").val( );
		if ( fileVal == null || fileVal == "" ) {
		    $("#errorSection").append( "<span class=\"errorMessage\">File Required</span><br/>" );
		    $("#errorSection").css( "display", "inline-block");
		$("#file_field_label").css( "color", " #EE3124" );
		result = false;
		}
		<?php
	    }?>
	    var authorVal = $("#author_field").val( );
	    if ( authorVal == null || authorVal == "" ) {
		$("#errorSection").append( "<span class=\"errorMessage\">Author Required</span><br/>" );
		$("#errorSection").css( "display", "inline-block");
		$("#author_field_label").css( "color", " #EE3124" );
		result = false;
	    }
	    var titleVal = $("#title_field").val( );
	    if ( titleVal == null || titleVal == "" ) {
		$("#errorSection").append( "<span class=\"errorMessage\">Title Required</span><br/>" );
		$("#errorSection").css( "display", "inline-block");
		$("#title_field_label").css( "color", " #EE3124" );
		result = false;
	    }
	    var yearVal = $("#year_field").val( );
	    if ( yearVal == null || yearVal == "" ) {
		$("#errorSection").append( "<span class=\"errorMessage\">Year Required</span><br/>" );
		$("#errorSection").css( "display", "inline-block");
		$("#year_field_label").css( "color", " #EE3124" );
		result = false;
	    }
	    return result;
	}

	
	</script>
	<form class="imageForm" method="POST" action="<?=$editImageActionUrl?>" enctype="multipart/form-data" onsubmit="return validate( )">
	      <div class="errorSection" id="errorSection"></div>
	      <br/>

	      <label id="author_field_label" for="author_field">Author</label>
	     <br/>
	     <input id="author_field" type="text" name="<?=EditImageAction::POST_PARAM_AUTHOR?>" value="<?=$author?>"/>
		  <?php parent::formatImageDataAutoComplete( DbConnectionUtil::getDbConnection( ), 'author', 'author_field' );?>
	     <br/>
	     <label id="title_field_label" for="title_field">Title</label>
	     <br/>
             <input id="title_field" type="text" name="<?=EditImageAction::POST_PARAM_TITLE?>" value="<?=$title?>"/>
             <br/>
             <label id="year_field_label" for="year_field">Year</label>
             <br/>
	     <input id="year_field" name="<?=EditImageAction::POST_PARAM_YEAR?>" value="<?=$year?>"/>
             <br/><br/>

	     <input id="attribute_list" type="hidden" name="<?=EditImageAction::POST_PARAM_ATTRIBUTE_LIST?>" value="<?=$attributeListString?>"/>
             <label for="attribute_table">Attributes</label>
	     <table class="attribute_table" id="attribute_list_table">
	     <?php
					      if ( $attributeList != NULL ) {
						  for( $i = 0; $i < count( $attributeList ); $i++ ) {
						      ?>
						      <tr>
						      <td><?=$attributeList[$i]->getAttribute( )?></td>
						      <td><a onclick="removeAttributeAtIndex(<?=$i?>);">Remove</a>
						      </tr>
						      <?php
						  }
					      }
             ?>
             </table>
             <br/>
	     <a class="button" onclick="addAttribute( );">Add Attribute</a>
	     <br/><br/>

	     <label id="thumbnail_field_label" for="thumbnail_field">Thumbnail</label>
	      <br/>
	      <div id="thumbnail_div">
	      <?php
	      if ( $thumbnailUri == NULL ) {
	      ?>
	         <input type="file" id="thumbnail_field" name="<?=EditImageAction::POST_PARAM_THUMBNAIL?>"/>
	      <?php
	      }
	      else {
	      ?>
		  <img src="<?=UrlFormatter::formatImageUrl($thumbnailUri)?>"/>
		  <br/>
		  <input type="button" onclick="replaceThumbnail( )" value="Replace"/>
	      <?php
	      }
	      ?>
	      </div>

             <label id="file_field_label" for="file_field">File</label>
             <br/>
	      <div id="file_div">
	      <?php
		    if ( $contentUri == NULL ) {
		    ?>
			<input type="file" id="file_field" name="<?=EditImageAction::POST_PARAM_FILE?>"/>
		    <?php
		    }
	            else {
		    ?>
			<img style="max-width: 100%" src="<?=UrlFormatter::formatImageUrl( $contentUri )?>"/>
			<br/>
		        <input type="button" onclick="replaceFile( )" value="Replace"/>
			<br/>
		    <?php
		    }
	      ?>
	      </div>	      
	     
	     <input class="button" type="submit" value="<?=$submitLabel?>"/>
	     <input class="button" type="button" onclick="cancel( )" value="Cancel"/>
	</form>
	<?php
	
	parent::displayFooter( );
    }
}

?>
