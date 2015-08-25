<?php
ClassLoader::requireClassOnce( 'model/DbData' );

class ImageAttribute extends DbData {
    private $attribute;
    private $imageDataId;

    public function __construct( $id = NULL ) {
	parent::__construct( $id );
    }

    public function getImageDataId( ) {
	return $this->imageDataId;
    }

    public function setImageDataId( $imageDataId ) {
	$this->imageDataId = $imageDataId;
    }

    public function getAttribute( ) {
	return $this->attribute;
    }

    public function setAttribute( $attribute ) {
	$this->attribute = $attribute;
    }

    protected function update( PDO $dbConnection ) {
	throw new Exception( "Unsupported Operation: update" );
    }

    protected function insert( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO image_attribute ( image_data_id, attribute ) VALUES ( :imageDataId, :attribute )' );
	$imageDataId = $this->getImageDataId( );
	$preparedStatement->bindParam( ':imageDataId', $imageDataId  );
	$attribute = $this->getAttribute( );
	$preparedStatement->bindParam( ':attribute', $attribute );
	$preparedStatement->execute( );
	parent::setId( $dbConnection->lastInsertId( ) );
	$preparedStatement = NULL;
    }

    public function delete( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'DELETE FROM image_attribute WHERE id = :id' );
	$id = parent::getId( );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
    }

    public static function loadImageAttributeListByImageDataId( PDO $dbConnection, $imageDataId ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT * FROM image_attribute WHERE image_data_id = :imageDataId' );
	$preparedStatement->bindParam( ':imageDataId', $imageDataId );
	$preparedStatement->execute( );
	$outputList = array( );
	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    $imageAttribute = new ImageAttribute( $resultRow['id'] );
	    $imageAttribute->setImageDataId( $imageDataId );
	    $imageAttribute->setAttribute( $resultRow['attribute'] );
	    $outputList[] = $imageAttribute;
	}
	$preparedStatement = NULL;
	return $outputList;
    }

    public static function loadExistingValues( PDO $dbConnection ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT DISTINCT attribute FROM image_attribute' );
	$preparedStatement->execute( );
	$resultRow = NULL;
	$existingValues = Array( );
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    $existingValues[] = $resultRow['attribute'];
	}
	$preparedStatement = NULL;
	return $existingValues;
    }

    public static function loadImageIdListByAttribute( PDO $dbConnection, $attribute ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT image_data_id FROM image_attribute WHERE attribute = :attribute' );
	$preparedStatement->bindParam( ':attribute', $attribute );
	$preparedStatement->execute( );
	$resultRow = NULL;
	$imageIdList = Array( );
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    $imageIdList[] = $resultRow['image_data_id'];
	}
	$preparedStatement = NULL;
	return $imageIdList;
    }

    public static function loadCommonAttributeImageIdTupleList( PDO $dbConnection, $sourceImageId, array $excludedImageIdList ) {
	$queryString = 'SELECT DISTINCT ima1.image_data_id as imageId, ima1.attribute as attribute FROM image_attribute ima1, image_attribute ima2 WHERE ima1.attribute = ima2.attribute AND ima1.image_data_id <> ima2.image_data_id AND ima2.image_data_id = :sourceImageId and ima1.image_data_id NOT IN ( :excludedImageIdList )';
	$preparedStatement = $dbConnection->prepare( $queryString );
	$preparedStatement->bindParam( ':sourceImageId', $sourceImageId );
	$preparedStatement->bindParam( ':excludedImageIdList', implode( $excludedImageIdList, "," ) );
	$preparedStatement->execute( );
	$resultList = Array( );
	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) ) {
	    $resultEntry = Array( );
	    $resultEntry['imageId'] = $resultRow['imageId'];
	    $resultEntry['attribute'] = $resultRow['attribute'];
	    $resultList[] = $resultEntry;
	}
	$preparedStatement = NULL;
	return $resultList;
    }

    public static function loadCommonAttributeImageIdList( PDO $dbConnection, $attribute, array $excludedImageIdList ) {
	$queryString = 'SELECT DISTINCT ima1.image_data_id as imageId FROM image_attribute ima1, image_attribute ima2 WHERE ima2.attribute = :attribute AND ima1.attribute = ima2.attribute AND ima1.image_data_id <> ima2.image_data_id AND ima1.image_data_id NOT IN ( :excludedImageIdList )';
	$preparedStatement = $dbConnection->prepare( $queryString );
	$preparedStatement->bindParam( ':attribute', $attribute );
	$preparedStatement->bindParam( ':excludedImageIdList', implode( $excludedImageIdList, "," ) );
	$preparedStatement->execute( );
	$resultList = Array( );
	$resultRow = NULL;
	while ( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	    $resultList[]= $resultRow['imageId'];
	$preparedStatement = NULL;
	return $resultList;
    }
}

?>