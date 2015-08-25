<?php
ClassLoader::requireClassOnce( 'util/IndexRoutingItem' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'util/RequestParser' );

/**
 * This class provides an action which provides access to style sheets to the requester.
 * This facilitates customized themes for users.
 * @author craigb
 */
class AccessStyleSheetAction extends IndexRoutingItem
{
    // This constant defines the default CSS file
    const DEFAULT_CSS_FILE = 'default.css';

    private $baseCssDir;

    /**
     * Constructs a new access style sheet object.
     */
    public function __construct( )
    {
	parent::__construct( FALSE );
	$this->baseCssDir = dirname( dirname( __FILE__ ) ) . '/css/';
    }

    /**
     * This function returns the index routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
	return 'AccessStyleSheet';
    }

    /**
     * This abstract function performs the processing of the user request.
     * @param User $user The requesting user. If the user is not null, then by convention
     * actions will assume the user is authenticated, otherwise not.
     * @throws Exception If an error was encountered while processing this request, an exception
     * will be thrown.
     * @return void
     */
    public function processRequest( User $user = NULL )
    {
	// TODO check to see if the user has a customized theme, load the corresponding stylesheet
	// for now, load the default stylesheet
	$this->displayStyleSheet( AccessStyleSheetAction::DEFAULT_CSS_FILE );
    }

    /**
     * Displays the style sheet at the specified path to the requester.
     * @param String $cssFileName The name of the CSS file to be displayed.
     * @return void
     */
    private function displayStyleSheet( $cssFileName )
    {
	$cssPath = $this->baseCssDir . $cssFileName;
	header( 'Content-type: text/css' );
	readfile( $cssPath );
    }
}

?>
