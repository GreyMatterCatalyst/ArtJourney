<?php
ClassLoader::requireClassOnce( 'views/View' );
ClassLoader::requireClassOnce( 'util/UrlFormatter' );

/**
 * This class encapsulates the functionality of displaying the landing page to the requester.
 * This is the default page users who aren't authenticated get sent to.
 * @author craigb
 */
class LandingPageView extends View
{
    /**
     * Constructs a new landing page view.
     */
    public function __construct( )
    {
		parent::__construct( FALSE );
    }
    
    /**
     * This function returns the routing key for the this class.
     * @return String The index routing key for the this class.
     */
    public static function getRoutingKey( )
    {
		return 'LandingPage';
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
	parent::displayHeader( $user, 'Welcome' );
	$splashImageFileNameList = array( );
	$splashImageFileNameList[] = '1.jpg';
	$splashImageFileNameList[] = '2.jpg';
	$splashImageFileNameList[] = '3.jpg';
	$splashImageFileNameList[] = '4.jpg';
	$splashImageNumber = rand( 0, count( $splashImageFileNameList ) - 1 );
	$splashImageName = $splashImageFileNameList[$splashImageNumber];
	$splashImageUrl = UrlFormatter::formatImageUrl( "splash/$splashImageName" );
	$startActionUrl = UrlFormatter::formatRoutingItemUrl( 'views/StartJourneyView' );
	?>
	<img style="padding-left: 10px; float: right; max-width: 50%; max-height: 700px;" src="<?=$splashImageUrl?>"/>
	<p>
	     The primary question examined by this project is, how does a contiguous experience of viewing artwork have an affect on one's perception of art in subsequent viewings. Experimenting with this idea, this application works interactively with the user to construct a unique contiguous art experience, and then asks the user to reflect on their experiences. A contiguous art experience is classified as a journey through art, comprised of viewings of a psuedo-randomly generated sequence of art pieces. The user begins their journey by being presented with a choice of random art pieces with distinct attributes. At each step along the journey, the user is presented with a full-scale viewing of the current art piece, along with a choice of three options to choose from, two comprised of random pieces which share attributes with the current piece being viewed, and a third completely random piece. As the user makes each choice, the user is advanced to a viewing of the chosen piece along with 3 new options. Throughout the journey this application tracks the most commonly chosen attribute by the user. After 6 pieces have been viewed, the user is shown their journey's history, their most commonly chosen attribute with matching art suggestions, and a prompt to give feedback about their journey, namely what emotions were elicited by the experience and what impressions they were left with. Finally, the user is able to give their journey a name, so that others can re-trace the steps of that particular journey if they so choose. This allows each user to construct a unique contiguous art experience, with some entropic computational guidance from the webapp itself, culminating with the an introspection by the user on how their experience has changed their own perceptions of art. 
	</p>
        <br/><br/>
        <a class="button" href="<?=$startActionUrl?>">Start Journey</a>
	<?php
	parent::displayFooter( );
    }
}

?>