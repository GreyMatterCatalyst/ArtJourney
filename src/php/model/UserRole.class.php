<?php

// includes
ClassLoader::requireClassOnce( 'model/DbData' );

/**
 * A simple struct/enum for user roles.
 * @author craigb
 */
class UserRole extends DbData
{
    /* These constants define the types of user roles */
    const USER_ROLE_TYPE_ADMIN = 0;
    const USER_ROLE_TYPE_VIEWER = 1;
    const USER_ROLE_TYPE_EDITOR = 2;
    
    private $userRoleTypeId;
    private $userId;

    /**
     * Constructs a new UserRole object.
     * @param $id The DB id of this user role object.
     * @param $userId The id of the user this role object corresponds to.
     * @param $userRoleTypeId The type of user role this object represents.
     */
    public function __construct( $id, $userId, $userRoleTypeId )
    {
	parent::__construct( $id );
	$this->userId = $userId;
	$this->userRoleTypeId = $userRoleTypeId;
    }

    /**
     * Returns the id of the user this role corresponds to.
     * @return The id of the user this role corresponds to.
     */
    public function getUserId( )
    {
	return $this->userId;
    }

    /**
     * Sets the id of the user associated with this user role.
     * @param $userId The id of the user associated with this user role.
     * @return void
     */
    public function setUserId( $userId )
    {
	$this->userId = $userId;
    }

    /**
     * Returns the type of role this objecr represents.
     * @return The type of role this objecr represents.
     */
    public function getUserRoleTypeId( )
    {
	return $this->userRoleTypeId;
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
	 // There should never be a reason to update an existing UserRole
	 throw new Exception( 'Unsupported function: update' );
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
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO user_roles ( user_id, user_role_type ) VALUES ( :userid, :userrole )' );
	$userId = $this->getUserId( );
	$preparedStatement->bindParam( ':userid', $userId );
	$userRoleTypeId = $this->getUserRoleTypeId( );
	$preparedStatement->bindParam( ':userrole', $userRoleTypeId );
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
	$preparedStatement = $dbConnection->prepare( 'DELETE FROM user_roles WHERE id = :id' );
	$id = parent::getId( );
	$preparedStatement->bindParam( ':id', $id );
	$preparedStatement->execute( );
	$preparedStatement = NULL;
    }

    /**
     * This function queries the DB for a list of user roles which match the specified user id. If
     * matching roles are found, it will populate and return a list UserRole objects.
     * @param PDO $dbConnection The DB connection this function will utilize.
     * @param int $userId The user id which will be utilized in the DB query.
     * @return array A list of populated user role objects. If none were found the list will be empty.
     * @throws PDOException If an error occurred while querying the DB, an exception will be thrown.
     */
    public static function loadUserRoleListByUserId( PDO $dbConnection, $userId )
    {
	$resultList = array( );
	$preparedStatement = $dbConnection->prepare( 'SELECT id, user_role_type FROM user_roles WHERE user_id = :userid' );
	$preparedStatement->bindParam( ':userid', $userId );
	$preparedStatement->execute( );

	$resultRow = NULL;
	while( ( $resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC ) ) )
	{
	    $userRole = new UserRole( $resultRow['id'], $userId, $resultRow['user_role_type'] );
	    $resultList[] = $userRole;
	}
	$preparedStatement = NULL;
	return $resultList;
    }
}
