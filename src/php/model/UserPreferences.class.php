<?php
ClassLoader::requireClassOnce( 'model/DbData' );

/**
 * This class encapsulates user preference data.
 * @author craigb
 */
class UserPreferences extends DbData
{
    private $userId;

    /**
     * Constructs a new user preferences object.
     * @param $id The DB id of this object.
     * @param $userId The id of the user this object corresponds to.
     */
    public function __construct( $id, $userId )
    {
	parent::__construct( $id );
	$this->userId = $userId;
    }

    /**
     * Returns the id of the user this object corresponds to.
     * @return The id of the user this object corresponds to.
     */
    public function getUserId( )
    {
	return $this->userId;
    }

    /**
     * Sets the id of the user this preferences object corresponds to.
     * @param int $userId The id of the user this preferences object corresponds to.
     * @return void
     */
    public function setUserId( $userId )
    {
	$this->userId = $userId;
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
	// TODO: update as needed when preferences are added.
	$preparedStatement = $dbConnection->prepare( 'UPDATE user_preferences WHERE id = :id ' );
	$id = parent::getId( );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
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
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO user_preferences ( user_id ) VALUES ( :userid )' );
	$userId = $this->getUserId( );
	$preparedStatement->bindParam( ':userid', $userId );
	$preparedStatement->execute( );
	parent::setId( $dbConnection->lastInsertId( ) );
	$preparedStatement = NULL;
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
	$preparedStatement = $dbConnection->prepare( 'DELETE FROM user_preferences WHERE id = :id' );
	$id = parent::getId( );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
    }

    /**
     * Queries the DB for the user preferences data which matches the specified user id. If found,
     * it will populate and return a UserPreferences object, otherwise it will return NULL.
     * @param PDO $dbConnection The DB connection this function will utilize.
     * @param int $userId The user id which will be utilized in the DB query.
     * @return If matching user preference data is found, a populated UserPreferences object, 
     * otherwise NULL.
     * @throws PDOException If an error occurred in the process of accessing the DB, an exception will be thrown.
     */
    public static function loadUserPreferencesByUserId( PDO $dbConnection, $userId )
    {
	// TODO fill in with fields as they are added
	$preparedStatement = $dbConnection->prepare( 'SELECT id FROM user_preferences WHERE user_id = :userid' );
	$preparedStatement->bindParam( ':userid', $userId );
	$preparedStatement->execute( );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	if ( $resultRow )
	{
	    $id = $resultRow['id'];
	    $userPreferences = new UserPreferences( $id, $userId );
	    $preparedStatement = NULL;
	    return $userPreferences;
	}
	$preparedStatement = NULL;
	return NULL;
    }
}
?>
