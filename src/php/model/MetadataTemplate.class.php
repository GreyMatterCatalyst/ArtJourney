<?php
ClassLoader::requireClassOnce( 'model/DbData' );

class MetadataTemplate extends DbData {
    private $contentType;
    private $species;
    private $stainingMethod;
    private $brainRegion;
    private $author;
    private $antibody;
    private $comments;
    private $templateName;

    public function __construct( $id = NULL ) {
	parent::__construct( $id );
    }

    public function getTemplateName( ) {
	return $this->templateName;
    }

    public function setTemplateName( $templateName ) {
	$this->templateName = $templateName;
    }

    public function getContentType( ) {
	return $this->contentType;
    }

    public function setContentType( $contentType ) {
	$this->contentType = $contentType;
    }

    public function getSpecies( ) {
	return $this->species;
    }

    public function setSpecies( $species ) {
	$this->species = $species;
    }

    public function getStainingMethod( ) {
	return $this->stainingMethod;
    }

    public function setStainingMethod( $stainingMethod ) {
	$this->stainingMethod = $stainingMethod;
    }

    public function getBrainRegion( ) {
	return $this->brainRegion;
    }

    public function setBrainRegion( $brainRegion ) {
	$this->brainRegion = $brainRegion;
    }

    public function getAuthor( ) {
	return $this->author;
    }

    public function setAuthor( $author ) {
	$this->author = $author;
    }

    public function getAntibody( ) {
	return $this->antibody;
    }

    public function setAntibody( $antibody ) {
	$this->antibody = $antibody;
    }

    public function getComments( ) {
	return $this->comments;
    }

    public function setComments( $comments ) {
	$this->comments = $comments;
    }

  
    protected function update( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'UPDATE metadata_template SET species = :species, staining_method = :stainingMethod, author = :author, brain_region = :brainRegion, antibody = :antibody, comments = :comments, content_type = :content_type, template_name = :templateName WHERE id = :id' );
	$species = $this->getSpecies( );
	$preparedStatement->bindParam( ':species', $species );
	$stainingMethod = $this->getStainingMethod( );
	$preparedStatement->bindParam( ':stainingMethod', $stainingMethod );
	$author = $this->getAuthor( );
	$preparedStatement->bindParam( ':author', $author );
	$brainRegion = $this->getBrainRegion( );
	$preparedStatement->bindParam( ':brainRegion', $brainRegion );
	$antibody = $this->getAntibody( );
	$preparedStatement->bindParam( ':antibody', $antibody );
	$comments = $this->getComments( );
	$preparedStatement->bindParam( ':comments', $comments );
	$contentType = $this->getContentType( );
	$preparedStatement->bindParam( ':content_type', $contentType );
	$templateName = $this->getTemplateName( );
	$preparedStatement->bindParam( ':templateName', $templateName );
	$id = parent::getId( );	
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
    }

    protected function insert( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO metadata_template ( species, staining_method, author, brain_region, antibody, comments, content_type, template_name ) VALUES ( :species, :stainingMethod, :author, :brainRegion, :antibody, :comments, :contentType, :templateName )' );
	$species = $this->getSpecies( );
	$preparedStatement->bindParam( ':species', $species );
	$stainingMethod = $this->getStainingMethod( );
	$preparedStatement->bindParam( ':stainingMethod', $stainingMethod );
	$author = $this->getAuthor( );
	$preparedStatement->bindParam( ':author', $author );
	$brainRegion = $this->getBrainRegion( );
	$preparedStatement->bindParam( ':brainRegion', $brainRegion );
	$antibody = $this->getAntibody( );
	$preparedStatement->bindParam( ':antibody', $antibody );
	$comments = $this->getComments( );
	$preparedStatement->bindParam( ':comments', $comments );
	$contentType = $this->getContentType( );
	$preparedStatement->bindParam( ':contentType', $contentType );
	$templateName = $this->getTemplateName( );
	$preparedStatement->bindParam( ':templateName', $templateName );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
	parent::setId( $dbConnection->lastInsertId( ) );	
    }

    public function delete( PDO $dbConnection ) {
	throw new Exception( "Unsupported action: delete" );
    }
    
    public static function loadMetadataTemplateById( PDO $dbConnection, $id ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT * FROM metadata_template WHERE id = :id' );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$metadataTemplate = new MetadataTemplate( $id );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	if ( $resultRow ) {
	    $metadataTemplate->setSpecies( $resultRow['species'] );
	    $metadataTemplate->setStainingMethod( $resultRow['staining_method'] );
	    $metadataTemplate->setAuthor( $resultRow['author'] );
	    $metadataTemplate->setBrainRegion( $resultRow['brain_region'] );
	    $metadataTemplate->setAntibody( $resultRow['antibody'] );
	    $metadataTemplate->setComments( $resultRow['comments'] );
	    $metadataTemplate->setContentType( $resultRow['content_type'] );
	    $metadataTemplate->setTemplateName( $resultRow['template_name'] );
	    $preparedStatement = NULL;
	    return $metadataTemplate;
	}
	else
	    return NULL;
    }

    public static function loadMetadataTemplateNameIdList( PDO $dbConnection ) {
	$resultList = array( );
	$preparedStatement = $dbConnection->prepare( 'SELECT id, template_name FROM metadata_template' );
	$preparedStatement->execute( );
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $resultList[$resultRow['template_name']] = $resultRow['id'];
	$preparedStatement = NULL;
	return $resultList;
    }
    
}

?>