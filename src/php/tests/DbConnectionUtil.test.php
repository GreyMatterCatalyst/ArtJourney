<?php
/**
 * This is a script to test the functionality within the DbConnectionUtil class.
 * @author craigb
 */

error_reporting( E_ALL| E_STRICT );
require_once( dirname( dirname( __FILE__ ) ) . '/util/ClassLoader.class.php' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );

print( "Starting DbConnectionUtil test...\n" );

print( "Establishing DB connection...\n" );

$dbConnection = DbConnectionUtil::getDbConnection( );

if ( $dbConnection != NULL )
{
    print( "DB connection established...\n" );

    print( "Running test query...\n" );
    $preparedStatement = $dbConnection->prepare( 'SELECT COUNT( id ) AS userCount FROM user' );
    $preparedStatement->execute( );
    $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
    if ( isset( $resultRow['userCount' ] ) )
    {
	print( "Test Query Successful: user count: " . $resultRow['userCount' ] . "\n" );
    }
    else
    {
	print( "Test Query FAILED.\n" );
	exit( -1 );
    }
    $preparedStatement = NULL;
    
    print( "Running test insertion...\n" );
    $preparedStatement = $dbConnection->prepare( 'INSERT INTO staining_method_data ( name ) VALUES ( :name )' );
    $name = 'testMethod';
    $preparedStatement->bindParam( ':name', $name );
    if ( !$preparedStatement->execute( ) )
    {
	throw new Exception( "INSERT failed, error_code: " . $dbConnection->errorCode( ) );
    }
    $lastInsertId = $dbConnection->lastInsertId( );
    $preparedStatement = NULL;
    print( "Test insertion successful, id: $lastInsertId\n" );

    print( "Running test deletion...\n" );
    $preparedStatement = $dbConnection->prepare( 'DELETE FROM staining_method_data WHERE id = :id' );
    $preparedStatement->bindParam( ':id', $lastInsertId );
    if ( !$preparedStatement->execute( ) )
    {
	throw new Exception( "DELETE failed, error_code: " . $dbConnection->errorCode( ) );
    }
    $preparedStatement = NULL;
    
    $preparedStatement = $dbConnection->prepare( 'SELECT * FROM staining_method_data WHERE id = :id' );
    $preparedStatement->bindParam( ':id', $lastInsertId );
    if ( !$preparedStatement->execute( ) )
    {
	throw new Exception( "SELECT failed, error_code: " . $dbConnection->errorCode( ) );
    }
    $result = $preparedStatement->fetch( PDO::FETCH_ASSOC );
    if ( $result )
    {
	printf( "Test deletion, failed to delete object with id: $lastInsertId FAILED\n" );
	exit( -1 );
    }
    $preparedStatement = NULL;
    
    
    print( "Test deletion successful.\n" );
    $preparedStatement = NULL;
}
else
{
    print( "Failed to establish DB connection, TEST FAILED.\n" );
    exit( -1 );
}

?>
