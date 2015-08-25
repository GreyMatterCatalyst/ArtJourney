<?php
ClassLoader::requireClassOnce( 'util/Settings' );

/**
 * This class encapsulates the functionality of establishing/retrieving a connection to the database.
 * @author craigb
 */
class DbConnectionUtil
{
    private static $dbConnection = NULL;

    /**
     * This function returns a reference to the database connection. If none exists, it will establish
     * the connection to the database.
     * @return A reference to the database connection.
     * @throws PDOException If an error occurred during the process of establishing the database connection.
     */
    public static function getDbConnection( )
    {
	// if the single DB connection has not yet been initialized, initialize it
	if ( DbConnectionUtil::$dbConnection == NULL )
	{
	    $dbConnectionString = 'mysql:host=' . Settings::getSetting( 'DB_URL' );
	    $dbConnectionString .= ';dbname=' . Settings::getSetting( 'DB_SCHEMA' );
	    DbConnectionUtil::$dbConnection = new PDO( $dbConnectionString, Settings::getSetting( 'DB_USERNAME' ), Settings::getSetting( 'DB_PASSWORD' ) );
	    DbConnectionUtil::$dbConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	return DbConnectionUtil::$dbConnection;
    }
}

?>
