<?php
ClassLoader::requireClassOnce( 'model/DbData' );
ClassLoader::requireClassOnce( 'model/UserRole' );
ClassLoader::requireClassOnce( 'model/UserPreferences' );

/**
 * This class encapsulates user data.
 * @author craigb
 */
class User extends DbData
{
    private $username;
    private $password;
    private $email;
    private $enabled;

    /**
     * Constructs a new User object.
     * @param int $id The DB id for this user.
     * @param String $username The username for this user.
     * @param String $password The password for this user.
     * @param String $email The email for this user.
     * @param Datetime $lastLogin The last login time for this user.
     * @param boolean $enabled Whether or not this user account is enabled.
     */
    public function __construct( $id, $username, $password, $email, $lastLogin, $enabled )
    {
	parent::__construct( $id );
	$this->username = $username;
	$this->password = $password;
	$this->email = $email;
	$this->lastLogin = $lastLogin;
	$this->enabled = $enabled;
    }

    /**
     * Gets the password for this user.
     * @return String The password for this user.
     */
    public function getPassword( ) {
	return $this->password;
    }

    /**
     * Sets the password for this user.
     * @param String $password The password to be set.
     */
    public function setPassword( $password ) {
	$this->password = $password;
    }

    /**
     * Sets this user's username.
     * @param String $username The username to be set.
     */
    public function setUsername( $username ) {
	$this->username = $username;
    }

    /**
     * Returns this user's username.
     * @return String This user's username.
     */
    public function getUsername( ) {
	return $this->username;
    }

    /**
     * Returns whether or not this user is enabled.
     * @return boolean Whether or not this user is enabled.
     */
    public function isEnabled( )
    {
	return $this->enabled;
    }

    /**
     * Sets whether or not this user is enabled.
     * @param boolean $enabled Whether or not this user is enabled.
     * @return void
     */
    public function setIsEnabled( $enabled )
    {
	$this->enabled = $enabled;
    }

    /**
     * Returns the email address for this user.
     * @return String The email address for this user.
     */
    public function getEmail( )
    {
	return $this->email;
    }

    /**
     * Sets the email address for this user.
     * @param String $email The email address to be set.
     * @return void
     */
    public function setEmail( $email )
    {
	$this->email = $email;
    }

    /**
     * Returns the last login for this user.
     * @return Datetime The last login for this user.
     */
    public function getLastLogin( )
    {
	return $this->lastLogin;
    }

    /**
     * Sets the last login for this user.
     * @param Datetime $lastLogin The last login to be set.
     * @return void
     */
    public function setLastLogin( $lastLogin )
    {
	$this->lastLogin = $lastLogin;
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
	$preparedStatement = $dbConnection->prepare( 'UPDATE user SET password = :password, email = :email, last_login = :lastlogin, enabled = :enabled WHERE id = :id' );
	$password = $this->getPassword( );
	$preparedStatement->bindParam( ':password', $password );
	$email = $this->getEmail( );
	$preparedStatement->bindParam( ':email', $email );
	$lastLogin = date( 'Y-m-d H:i:s', $this->getLastLogin( ) );
	$preparedStatement->bindParam( ':lastlogin', $lastLogin );
	$isEnabled = $this->isEnabled( );
	$preparedStatement->bindParam( ':enabled', $isEnabled );
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
	// insert  the new user data
	$preparedStatement = $dbConnection->prepare( 'INSERT INTO user ( username, password, email, enabled ) VALUES ( :username, :password, :email, :enabled )' );
	$username = $this->getUsername( );
	$preparedStatement->bindParam( ':username', $username );
	$password = $this->getPassword( );
	$preparedStatement->bindParam( ':password', $password );
	$email = $this->getEmail( );
	$preparedStatement->bindParam( ':email', $email );
	$isEnabled = $this->isEnabled( );
	$preparedStatement->bindParam( ':enabled', $isEnabled );
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
	// This operation is not supported for users, instead set enabled to false. This is due to
	// design of having user's image persist past when they'll be using the system.
	throw new Exception( 'Unsupported operation: delete' );
    }

    /**
     * Queries the DB for the user object matching the specified user id. If found will
     * load and populate a User object with associated role and preference 
     * @param PDO $dbConnection The DB connection this function will utilize.
     * @param int $userId The user id to be used in the query for a user.
     * @return If a matching user was found, a populated User object, otherwise NULL.
     * @throws PDOException If an error occurred while attempting to query the DB
     * an exception will be thrown.
     */
    public static function loadUserById( PDO $dbConnection, $userId )
    {
	$preparedStatement = $dbConnection->prepare( 'SELECT username, password, email, last_login, enabled FROM user WHERE id = :userid' );
	$preparedStatement->bindParam( ':userid', $userId );
	$preparedStatement->execute( );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	if ( $resultRow )
	{
	    // load user data
	    $user = new User( $userId, $resultRow['username'], $resultRow['password'], $resultRow['email'], $resultRow['last_login'], $resultRow['enabled'] );
	    $preparedStatement = NULL;
	    return $user;
	}

	$preparedStatement = NULL;
	return NULL;
    }

    /**
     * Attempts to retrieve a user object based upon the specified username.
     * @param PDO $dbConnection The DB connection this function will utilize.
     * @param String $username The username to be utilized in loading
     */
    public static function loadUserByUsername( PDO $dbConnection, $username ) {
	$preparedStatement = $dbConnection->prepare( 'SELECT id FROM user WHERE username = :username' );
	$preparedStatement->bindParam( ':username', $username );
	$preparedStatement->execute( );
	$resultRow = $preparedStatement->fetch( PDO::FETCH_ASSOC );
	if ( $resultRow ) {
	    $userId = $resultRow['id'];
	    return self::loadUserById( $dbConnection, $userId );
	}

	$preparedStatement = NULL;
	return NULL;
    }
}

?>
