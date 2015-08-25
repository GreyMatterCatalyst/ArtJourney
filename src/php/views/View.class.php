<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'model/ImageData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );

/**
 * This abstract parent class encapsulates the functionality in common with view classes.
 * @author craigb
 */
abstract class View extends IndexRoutingItem
{
    private $menuItemList;

    /**
     * @param boolean $requiresAuthentication Whether or not this action requires an authenticated user.
     * @param array $requiredRoleTypeList If user authentication is required, this list defines the
     * roles which are required for user authorization. Defaults to NULL.
     */
    public function __construct( $requiresAuthentication, array $requiredRoleTypeList = NULL )
    {
	parent::__construct( $requiresAuthentication, $requiredRoleTypeList );
	$this->menuItemList = array( );
	
	$this->menuItemList['Start Journey'] = array( 
	    'REQUIRES_USER' => FALSE, 
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'views/StartJourneyView' ) );
	$this->menuItemList['View Journeys'] = array(
	    'REQUIRES_USER' => FALSE,
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'views/ListJourneysView' ) );
	$this->menuItemList['Image Search'] = array( 
	    'REQUIRES_USER' => FALSE, 
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'views/ImageSearchView' ) );
	$this->menuItemList['Add New Image'] = array( 
	    'REQUIRES_USER' => TRUE, 
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'views/EditImageView' ) );
	$this->menuItemList['About'] = array(
	    'REQUIRES_USER' => FALSE,
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'views/AboutView' ) );
	$this->menuItemList['Logout'] = array( 
	    'REQUIRES_USER' => TRUE, 
	    'URL' => UrlFormatter::formatRoutingItemUrl( 'actions/LogoutAction' ) );
    }

    /**
     * Displays the header section, including opening tags, as well as the header and main menu
     * @param User $user If specified and not NULL, this will determine whether or not to display
     * options for authenticated users.
     * @param String $pageName The name of the page the header is being displayed for.
     * @return void
     */
    protected function displayHeader( User $user = NULL, $pageName )
    {
        $cssUrl = UrlFormatter::formatRoutingItemUrl( 'actions/AccessStyleSheetAction' );
        $logoUrl = UrlFormatter::formatImageUrl( 'ui/teamstrausfeld.jpeg' );
        $siteTitle = "Journey Through Art";
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
         <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
         <head>
         <title><?=$siteTitle?></title>
         <link rel="stylesheet" type="text/css" href="<?=$cssUrl?>"/>
         <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
	 <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
	 <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
         </head>
         <body>
         <div class="headerSection">
             
             <div class="headerTitle">
                 <a href="<?=UrlFormatter::getBaseUrl( )?>"><?=$siteTitle?></a>
             </div>
	     <div class="headerUserSection">
             <?php
             if ( $user != NULL )
             {
                $username = $user->getUsername( );
                ?>
                User: <?=$username?>
		<?php
             }
	     else {
		 $loginActionUrl = UrlFormatter::formatRoutingItemUrl( 'views/LoginView' );
		 ?>
		 <a href="<?=$loginActionUrl?>">Admin</a>
		 <?php
	     }
	     
	     ?>
	     </div>
             
         <?php
         // TODO add logout link if the user is authenticated
         // TODO add account settings link
         // TODO add admin view link if the user has admin role
         ?>
         </div>
         <nav>
         <?php
	 foreach( $this->menuItemList as $menuItemName => $menuItem )
	 {
	     if ( !$menuItem['REQUIRES_USER'] || $user != NULL ) {
	     ?>
		<a href="<?=$menuItem['URL']?>"><?=$menuItemName?></a>
	     <?php
	     }
	 }
        ?>
        </nav>
        <div class="contentSection">
        <div class="contentSectionHeader">
        <p><?=$pageName?></p>
        </div>
        <?php
    }

    /**
     * Displays the footer section of the site.
     * @return void
     */
    protected function displayFooter( )
    {
	$footerLogoUrl = UrlFormatter::formatImageUrl( 'ui/footer_ua_logo.png' );
	?>
	</div>
    <div style="clear:both"></div>
	<div class="footerSection">
		<img src="<?=$footerLogoUrl?>"/>
	</div>
    </body>
    </html>
	<?php
    }

    protected static function generateAutoCompleteScript( array $existingFieldValueList, $inputDOMId ) {
	$listSize = count( $existingFieldValueList );
	?>
	<script type="text/javascript">
	     $("#<?=$inputDOMId?>").autocomplete( {
		 delay: 0,
		 minLength: 0,
		 source : [
		     <?php
		     for( $i = 0; $i < $listSize; $i++ ) {
			 ?>
			 "<?=$existingFieldValueList[$i]?>"
			 <?php
			 if ( $i < $listSize - 1 ) {
			    ?>, <?php
			 }			      
		     }			 
		     ?>
		 ] } );
	</script>
	<?php
    }

    protected static function formatImageAttributeDataAutoComplete( PDO $dbConnection, $inputDOMId ) {
	$existingFieldValueList = ImageAttribute::loadExistingValues( $dbConnection );
	self::generateAutoCompleteScript( $existingFieldValueList, $inputDOMId );
    }

    protected static function formatImageDataAutoComplete( PDO $dbConnection, $dbFieldName, $inputDOMId ) {
	$existingFieldValueList = ImageData::loadExistingFieldValues( $dbConnection, $dbFieldName );
	self::generateAutoCompleteScript( $existingFieldValueList, $inputDOMId );	
    }
}

?>
