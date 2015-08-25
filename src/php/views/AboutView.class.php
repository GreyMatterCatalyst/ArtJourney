<?php
ClassLoader::requireClassOnce( 'views/View' );

class AboutView extends View {
    public function __construct( ) {
	parent::__construct( FALSE );
    }

    public static function getRoutingKey( ) {
	return 'AboutView';
    }

    public function processRequest( User $user = NULL ) {
	parent::displayHeader( $user, 'About' );

	?>
	<div class="centerWrapper">
	     <div>
	     <h3>Journey Through Art</h3>
	     <p style="display:inline-block;text-align:center;width:70%">
	     This project was conceived as a result of a final project for the course: ISTA301 Computational Art, a course offered by the <a href="http://sista.arizona.edu/academics/courses/">SISTA</a> department at the University of Arizona. The art pieces contained within this application were selected by the project's curators in order to provide a broad spectrum of art to choose from.
	     </p>
             <h3>Credits</h3>
             <div style="display:inline-block" class="searchResultItem">
             <span style="font-weight:bold;">Designer/Curator</span>
             <br/>
             Amanda Brooke Zucker
             <br/>
             <a href="mailto:azucker@email.arizona.edu">azucker@email.arizona.edu</a>
             </div>
             <div style="clear:both;"></div>
             <div style="display:inline-block" class="searchResultItem">
             <span style="font-weight:bold;">Designer/Curator</span>
             <br/>
             Kelley Kristine Burch
             <br/>
             <a href="mailto:kkburch@email.arizona.edu">kkburch@email.arizona.edu</a>
             </div>
             <div style="clear:both;"></div>
             <div style="display:inline-block" class="searchResultItem">
             <span style="font-weight:bold;">Designer/Programmer</span>
             <br/>
             Craig Barber
             <br/>
             <a href="mailto:craigb@email.arizona.edu">craigb@email.arizona.edu</a>
             </div>             
	     </div>
	</div>
	<?php

	parent::displayFooter( );
    }
}
?>