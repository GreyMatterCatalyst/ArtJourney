<?php
ClassLoader::requireClassOnce( 'model/DbData' );
ClassLoader::requireClassOnce( 'model/ImageAttribute' );

/**
 * This class encapsulates image data.
 * @author craigb
 */
class ImageData extends DbData
{
    private $contentType;
    private $submitterUserId;
    private $contentUri;
    private $thumbnailUri;
    private $author;
    private $title;
    private $year;
    private $attributeList;

    /**
     * Constructs a new ImageData object.
     * @param int $id The DB id of this object. Defaults to NULL.
     */
    public function __construct( $id = NULL )
    {
	parent::__construct( $id );
	$this->attributeList = array( );
    }

    public function addAttribute( ImageAttribute $attribute ) {
	$this->attributeList[] = $attribute;
    }

    public function addAttributeByString( $attributeString ) {
	$newImageAttribute = new ImageAttribute( NULL );
	$newImageAttribute->setImageDataId( parent::getId( ) );
	$newImageAttribute->setAttribute( $attributeString );
	$this->addAttribute( $newImageAttribute );
    }

    public function getAttributeList( ) {
	return $this->attributeList;
    }

    public function setAttributeList( array $attributeList ) {
	$this->attributeList = $attributeList;
    }

    public function removeAttributeByString( $attributeString ) {
	foreach( $this->attributeList as $key => $attribute ) {
	    if( $attribute->getAttribute( ) == $attributeString ) {
		unset( $this->attributeList[$key] );
		return $attribute;
	    }
	}

	return NULL;
    }

    public function hasAttribute( $attributeString ) {
	foreach ( $this->attributeList as $key => $attribute ) {
	    if ( $attribute->getAttribute( ) == $attributeString ) 
		return TRUE;
	}

	return FALSE;
    }

    /**
     * Returns the DB id of the submitting user of this image data.
     * @return int The DB id of the submitting user of this image data.
     */
    public function getSubmitterUserId( )
    {
	return $this->submitterUserId;
    }

    /**
     * Sets the DB id of the submitting user of this image data.
     * @param int $userId The DB id of the submitting user of this image data.
     */
    public function setSubmitterUserId( $userId )
    {
	$this->submitterUserId = $userId;
    }

    /**
     * Returns the URI of the content for this image.
     * @return String The URI of the content for this image.
     */
    public function getContentUri( )
    {
	return $this->contentUri;
    }

    /**
     * Sets the URI of the content for this image.
     * @param String The URI of the content for this image.
     * @return void
     */
    public function setContentUri( $contentUri )
    {
	$this->contentUri = $contentUri;
    }

    /**
     * Returns the URI of the thumbnail for this image.
     * @return String The URI of the thumbnail for this image.
     */
    public function getThumbnailUri( )
    {
	return $this->thumbnailUri;
    }

    /**
     * Sets the URI of the thumbnail for this image.
     * @param String The URI of the thumbnail for this image.
     * @return void
     */
    public function setThumbnailUri( $thumbnailUri )
    {
	$this->thumbnailUri = $thumbnailUri;
    }

    /**
     * Returns the author information for this image.
     * @return String The author information for this image.
     */
    public function getAuthor( )
    {
	return $this->author;
    }

    /**
     * Sets the author information for this image.
     * @param String $author The author information for this image.
     * @return void
     */
    public function setAuthor( $author )
    {
	$this->author = $author;
    }

    public function getTitle( ) {
	return $this->title;
    }

    public function setTitle( $title ) {
	$this->title = $title;
    }

    public function getYear( ) {
	return $this->year;
    }

    public function setYear( $year ) {
	$this->year = $year;
    }


    /**
     * This function performs an update of this object's data in the DB.
     * @param PDO $dbConnection The database connection this object will utilize to
     * process the update.
     * @return void
     * @throws Exception If an error is encountered in the process, an exception will be thrown.
     */
    protected function update( PDO $dbConnection )
    {
	$preparedStatement = $dbConnection->prepare( 'UPDATE image_data SET content_uri = :contenturi, thumbnail_uri = :thumbnail, author = :author, title = :title, year = :year WHERE id = :id' );
	$contentUri = $this->getContentUri( );
	$preparedStatement->bindParam( ':contenturi', $contentUri );
	$thumbnailUri = $this->getThumbnailUri( );
	$preparedStatement->bindParam( ':thumbnail', $thumbnailUri );
	$author = $this->getAuthor( );
	$preparedStatement->bindParam( ':author', $author );
	$title = $this->getTitle( );
	$preparedStatement->bindParam( ':title', $title );
	$year = $this->getYear( );
	$preparedStatement->bindParam( ':year', $year );
	$id = parent::getId( );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;

	foreach( $this->getAttributeList( ) as $attribute ) {
	    if ( $attribute->isNew( ) ) {
		$attribute->setImageDataId( parent::getId( ) );
		$attribute->save( $dbConnection );
	    }
	}
    }

    /**
     * This function performs a new insertion of this object's data into the DB. It is the implementing
     * class's responsibilty to set the newly generated DB id after insertion, (this is due to synchronicity
     * dependencies which may arise from potential insertions of child objects). 
     * @param PDO $dbConnection The database connection this object will utilize to process
     * the insertion.
     * @return void
     * @throws Excetpion If an error is encountered in the process, an exception will be thrown.
     */
    protected function insert( PDO $dbConnection )
    {
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO image_data ( submitter_user_id, content_uri, thumbnail_uri, author, title, year ) VALUES ( :submitteruserid, :contenturi, :thumbnail, :author, :title, :year )' );
	$submitterUserId = $this->getSubmitterUserId( );
	$preparedStatement->bindParam( ':submitteruserid', $submitterUserId ); 
	$contentUri = $this->getContentUri( );
	$preparedStatement->bindParam( ':contenturi', $contentUri );
	$thumbnailUri = $this->getThumbnailUri( );
	$preparedStatement->bindParam( ':thumbnail', $thumbnailUri );
	$author = $this->getAuthor( );
	$preparedStatement->bindParam( ':author', $author );
	$title = $this->getTitle( );
	$preparedStatement->bindParam( ':title', $title );
	$year = $this->getYear( );
	$preparedStatement->bindParam( ':year', $year );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
	parent::setId( $dbConnection->lastInsertId( ) );

	foreach( $this->attributeList as $attribute ) {
	    $attribute->setImageDataId( parent::getId( ) );
	    $attribute->save( $dbConnection );
	}
    }

    /**
     * Causes this DbData object to delete itself from the DB.
     * @param PDF $dbConnection The database conneciton this object will utilize to process
     * the deletion.
     * @return void
     * @throws Exception If an error is countered in the process, an exception will be thrown.
     */
    public function delete( PDO $dbConnection )
    {
	throw new Exception( "Unsupported action: delete" );
    }

    /**
     * Queries the DB for an image data object matching the specified user id. If found,
     * populates and returns an ImageData object, otherwise returns NULL.
     * @param PDO $dbConnection The DB connection this function will utilize.
     * @param int $imageId The DB id of the image being queried for.
     * @return ImageData If a matching image object was found, a populated ImageData object,
     * otherwise NULL.
     */
    public static function loadImageDataById( PDO $dbConnection, $imageId )
    {
	$preparedStatement = $dbConnection->prepare( 'SELECT * FROM image_data WHERE id = :id' );
	$preparedStatement->bindParam( ':id', $imageId );
	$preparedStatement->execute( );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	$imageData = NULL;
	if ( $resultRow )
	    $imageData = ImageData::populateImageDataByDbResultRow( $resultRow, $dbConnection );
	$preparedStatement = NULL;
	return $imageData;
    }

    /**
     * This is a private helper function for populating an ImageData obejct given
     * the specified DB result row.
     * @param int $imageId The DB id of the image being populated.
     * @param array $resultRow The DB query result row this function will consume data from.
     * @return ImageData A populated image data object.
     */
    private static function populateImageDataByDbResultRow( array $resultRow, PDO $dbConnection )
    {
	$imageData = new ImageData( $resultRow['id'] );
	$imageData->setContentUri( $resultRow['content_uri'] );
	$imageData->setSubmitterUserId( $resultRow['submitter_user_id'] );
	$imageData->setThumbnailUri( $resultRow['thumbnail_uri'] );
	$imageData->setAuthor( $resultRow['author'] );
	$imageData->setTitle( $resultRow['title'] );
	$imageData->setYear( $resultRow['year'] );
	$imageData->setAttributeList( ImageAttribute::loadImageAttributeListByImageDataId( $dbConnection, $imageData->getId( ) ) );	
	return $imageData;
    }

    public static function loadImageDataListByIdSet( PDO $dbConnection, array $imageIdSet )
    {
	// TODO filter image id set to protect against SQL injection
	$dbQueryString = 'SELECT * FROM image_data WHERE id IN (';
	$imageIdSetSize = count( $imageIdSet );
	for( $i = 0; $i < $imageIdSetSize; $i++ )
	{
	    $dbQueryString .= $imageIdSet[$i];
	    if ( $i < $imageIdSetSize - 1 )
		$dbQueryString .= ',';
	}
	$dbQueryString .= ')';

	$preparedStatement = $dbConnection->prepare( $dbQueryString );
	$preparedStatement->execute( );
	
	$imageDataResultList = array( );
	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $imageDataResultList[] = ImageData::populateImageDataByDbResultRow( $resultRow, $dbConnection );

	$preparedStatement = NULL;
	return $imageDataResultList;
    }

    public static function loadAllImageIdList( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT id FROM image_data' );
	$preparedStatement->execute( );
	$imageIdList = Array( );
	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    $imageIdList[] = $resultRow['id'];
	}
	$preparedStatement = NULL;
	return $imageIdList;
    }

    public static function loadExistingFieldValues( PDO $dbConnection, $dbFieldName ) {
	$dbQueryString = "SELECT DISTINCT $dbFieldName FROM image_data WHERE $dbFieldName <> ''";
	$preparedStatement = $dbConnection->prepare( $dbQueryString );
	$preparedStatement->execute( );
	$fieldValueResults = array( );
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $fieldValueResults[] = $resultRow[$dbFieldName];
	$preparedStatement = NULL;
	return $fieldValueResults;	    
    }
}
?>