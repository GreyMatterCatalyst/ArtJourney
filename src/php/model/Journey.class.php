<?php
ClassLoader::requireClassOnce( 'model/DbData' );

class Journey extends DbData {
    private $title;
    private $comments;
    private $creationDate;
    private $imageIdList;

    public function __construct( $id = NULL ) {
	parent::setId( $id );	
	$this->imageIdList = array( );
    }

    public function hasImageId( $imageId ) {
	foreach( $this->imageIdList as $currentImageId ) {
	    if ( $currentImageId == $imageId )
		return TRUE;
	}

	return FALSE;
    }

    public function addImageId( $imageId ) {
	if ( !$this->hasImageId( $imageId ) ) {
	    $this->imageIdList[] = $imageId;
	}
    }

    public function getImageIdList( ) {
	return $this->imageIdList;
    }

    public function getTitle( ) {
	return $this->title;
    }

    public function setTitle( $title ) {
	$this->title = $title;
    }

    public function getComments( ) {
	return $this->comments;
    }

    public function setComments( $comments ) {
	$this->comments = $comments;
    }

    public function getCreationDate( ) {
	return $this->creationDate;
    }

    public function setCreationDate( $creationDate ) {
	$this->creationDate = $creationDate;
    }

    protected function update( PDO $dbConnection ) {
	throw new Exception( "Unsupported acion: update" );
    }

    protected function insert( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO journey ( title, comments, creation_date ) VALUES( :title, :comments, :creationDate )' );
	$title = $this->getTitle( );
	$preparedStatement->bindParam( ':title', $title );
	$comments = $this->getComments( );
	$preparedStatement->bindParam( ':comments', $comments );
	$creationDate = date( 'Y-m-d H:i:s', $this->getCreationDate( ) );
	$preparedStatement->bindParam( ':creationDate', $creationDate );
	$preparedStatement->execute( );
	parent::setId( $dbConnection->lastInsertId( ) );
	$preparedStatement = NULL;
	
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO journey_image_data_assoc ( journey_id, image_data_id ) VALUES( :journeyId, :imageDataId )' );
	foreach( $this->imageIdList as $imageId ) {
	    $preparedStatement->bindParam( ':journeyId', parent::getId( ) );
	    $preparedStatement->bindParam( ':imageDataId', $imageId );
	    $preparedStatement->execute( );
	}
	$preparedStatement = NULL;
    }

    public function delete( PDO $dbConnection ) {
	throw new Exception( "Unsupported action: delete" );
    }

    private static function populateJourneyFromResultSet( PDO $dbConnection, $resultSet ) {
	$journeyData = new Journey( $resultSet['id'] );
	$journeyData->setTitle( $resultSet['title'] );
	$journeyData->setComments( $resultSet['comments'] );
	$journeyData->setCreationDate( $resultSet['creation_date'] );
	$stmt = $dbConnection->prepare( 'SELECT image_data_id FROM journey_image_data_assoc WHERE journey_id = :id' );
	$stmt->bindParam( ':id', $journeyData->getId( ) );
	$stmt->execute( );
	$resultRow = NULL;
	while( ( $resultRow = $stmt->fetch( PDO::FETCH_ASSOC ) ) )
	    $journeyData->addImageId( $resultRow['image_data_id'] );
	$stmt = NULL;
	return $journeyData;
    }

    public static function loadJourneyById( PDO $dbConnection, $journeyId ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT id, title, comments, creation_date FROM journey WHERE id = :id' );
	$preparedStatement->bindParam( ':id', $journeyId );
	$preparedStatement->execute( );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	$journeyData = NULL;
	if ( $resultRow )
	    $journeyData = self::populateJourneyFromResultSet( $dbConnection, $resultRow );
	$preparedStatement = NULL;
	return $journeyData;	
    }

    /** BUG: This only seems to load a single journey, when I have some time I'll come back and figure
	out why.
    **/
    public static function loadJourneyListByIdSet( PDO $dbConnection, array $journeyIdList ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT id, title, comments, creation_date FROM journey WHERE id IN ( :idList )' );
	print( "<br>" . implode( $journeyIdList, "," ) . "<br/>" );
	print( "<br/>{$preparedStatement->queryString}<br/>" );
	$preparedStatement->bindParam( ':idList', implode( $journeyIdList, "," ) );
	$preparedStatement->execute( );
	$resultList = Array( );
	$resultRow = NULL;
	while ( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    print( 'foo<br/>' );
	    $resultList[] = self::populateJourneyFromResultSet( $dbConnection, $resultRow );
	}
	$preparedStatement = NULL;
	return $resultList;
    }

    public static function loadAllJourneyIdList( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT id FROM journey' );
	$preparedStatement->execute( );
	$resultList = Array( );
	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $resultList[]= $resultRow['id'];
	$preparedStatement = NULL;
	return $resultList;	
    }
}