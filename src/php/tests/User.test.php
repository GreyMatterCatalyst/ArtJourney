<?php
/**
 * This is a test script which tests creation, insertion, updating, and retrieval of the class: User.class.php
 * @author craigb
 */

error_reporting( E_ALL | E_STRICT );

// includes
require_once( dirname( dirname( __FILE__ ) ) . '/util/ClassLoader.class.php' );
ClassLoader::requireClassOnce( 'model/User' );
ClassLoader::requireClassOnce( 'model/UserRole' );
ClassLoader::requireClassOnce( 'model/UserPreferences' );
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );

// create a new test user
printf( "Testing user creation..." );
$testNetId = 'testNetId';
$testEmail = 'test@email.com';
$testRealName = 'Test User';
$testRoleTypes = array( UserRole::USER_ROLE_TYPE_EDITOR, UserRole::USER_ROLE_TYPE_VIEWER );
$testUser = User::generateNewUser( $testNetId, $testEmail, $testRealName, $testRoleTypes, TRUE );
print( "PASSED\n" );

// insert the new user
printf( "Testing user insertion..." );
$dbConnection = DbConnectionUtil::getDbConnection( );
$testUser->save( $dbConnection );
print( "PASSED\n" );

// retrieve the user
$userId = $testUser->getId( );
printf( "Testing user retrieval, id: $userId ..." );
$testUser = NULL;
$testUser = User::loadUserByNetId( $dbConnection, $testNetId );

// verify the retrieve user data
if ( !$testUser )
{
    print( "FAILED: did not load user data\n" );
    exit( -1 );
}
if ( $userId != $testUser->getId( ) )
{
    print( "FAILED: retrieved id did not match, original: $userId retrieved: {$testUser->getId( )}\n" );
    exit( -1 );
}
foreach( $testRoleTypes as $testRoleType )
{
    if ( !$testUser->hasRole( $testRoleType ) )
    {
	print( "FAILED: did not load UserRoleType: " . $testRoleType . "\n" );
	exit( -1 );
    }
}
if ( $testUser->getEmail( ) != $testEmail )
{
    print( "FAILED: retrieved email did not match, original: $testEmail retrieved: {$testUser->getEmail( )}\n" );
    exit( -1 );
}
if ( $testUser->getRealName( ) != $testRealName )
{
    print( "FAILED: retrieved real name did not match, original: $testRealName retrieved: {$testUser->getRealName( )}\n" );
    exit( -1 );
}


if ( !$testUser->getPreferences( ) )
{
    print( "FAILED: did not load UserPreferences\n" );
    exit( -1 );
}

print( "PASSED\n" );

// update the user
print( "Testing user update, id: $userId ..." );
$lastLoginTime = time( );
$testUser->setLastLogin( $lastLoginTime );
$testUser->save( $dbConnection );

// retrieve the user again, verify the data has changed
$testUser = NULL;
$testUser = User::loadUserById( $dbConnection, $userId );
if ( !$testUser )
{
    print( "FAILED: did not load user, id: $userId\n" );
    exit( -1 );
}
// verify the modified field was saved
if ( $lastLoginTime != strtotime( $testUser->getLastLogin( ) ) )
{
    print( "FAILED: lastLoginTime did not match, original: $lastLoginTime retrieved: " . strtotime( $testUser->getLastLoginTime( ) ) . "\n" );
    exit( -1 );
}
print( "PASSED\n" );

// cleanup 
print( "Performing cleanup:\n" );
print( "Removing user roles..." );
foreach( $testRoleTypes as $testRoleType )
{
    $userRole = $testUser->removeRoleByType( $testRoleType );
    $userRole->delete( $dbConnection );
}
print( "SUCCESS\n" );
print( "Removing user preference data..." );
$userPreferences = $testUser->getPreferences( );
$userPreferences->delete( $dbConnection );
print( "SUCCESS\n" );
print( "Removing user data..." );
$preparedStatement = $dbConnection->prepare( 'DELETE FROM user WHERE id = :id' );
$preparedStatement->bindParam( ':id', $userId );
$preparedStatement->execute( );
$preparedStatement = NULL;
print( "SUCCESS\n" );
print( "All tests PASSED\n" );
?>
