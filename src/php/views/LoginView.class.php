<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );
ClassLoader::requireClassOnce( 'actions/LoginAction' );

class LoginView extends View {

    private $previousAttemptErrorMessage = NULL;

    public function __construct( ) {
	parent::__construct( FALSE );
    }
    
    public static function getRoutingKey( ) {
	return 'LoginView';
    }

    public function setPreviousAttemptErrorMessage( $errorMessage ) {
	$this->previousAttemptErrorMessage = $errorMessage;
    }

    public function  processRequest( User $user = NULL ) {
	parent::displayHeader( $user, 'Login' );
	$loginActionUrl = UrlFormatter::formatRoutingItemUrl( 'actions/LoginAction' );
	$usernameField = LoginAction::POST_PARAM_USERNAME;
	$passwordField = LoginAction::POST_PARAM_PASSWORD;

	?>
	<script type="text/javascript">
	     function validate( ) {
	       var result = true;
	       $("#errorSection").html( "" );
	       var usernameValue = $("#<?=$usernameField?>").val( );
	       if ( usernameValue == null || usernameValue == "" ) {
		   $("#errorSection").append( "<span class=\"errorMessage\">Username Required</span><br/>" );
		   $("#errorSection").css( "display", "inline-block" );
		   result = false;
	       }
	       var passwordValue = $("#<?=$passwordField?>").val( );
	       if( passwordValue == null || passwordValue == "" ) {
		   $("#errorSection").append( "<span class=\"errorMessage\">Password Required</span><br/>" );
		   $("#errorSection").css( "display", "inline-block" );
		   result = false;
	       }
	       return result;
	     }
	</script>
	<form class="imageForm" method="POST" action="<?=$loginActionUrl?>" onsubmit="return validate( )">
	      <div class="errorSection" id="errorSection"></div>
	      <?php
	     if ( $this->previousAttemptErrorMessage != NULL ) {
		 ?>
		 <script type="text/javascript">
		 $("#errorSection").html( "" );
		 $("#errorSection").append( "<span class=\"errorMessage\"><?=$this->previousAttemptErrorMessage?></span>" );
		 $("#errorSection").css( "display", "inline-block" );
		 </script>
		 <?php
	     }
	     ?>
	     <br/>
	     <label for="<?=$usernameField?>">Username</label>
             <br/>
             <input id="<?=$usernameField?>" type="text" name="<?=$usernameField?>"/>
	     <script type="text/javascript">$("#<?=$usernameField?>").focus( );</script>
             <br/>
             <label for="<?=$passwordField?>">Password</label>
             <br/>
             <input id="<?=$passwordField?>" type="password" name="<?=$passwordField?>"/>
             <br/>
	     <input type="submit" value="Login"/>
        </form>
									     
	<?php
	parent::displayFooter( );
    }
}
?>