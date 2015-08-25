<?php
/**
 * This defines an abstract parent class for any classes which represent DB
 * data.
 * @author craigb
 */
abstract class DbData
{
    private $id;

    /**
     * Constructs a new DbData object.
     * @param $id The DB id of this object.
     */
    public function __construct( $id = NULL )
    {
	$this->id = $id;
    }


    /**
     * Returns whether or not this object is a new object.
     * @return boolean Whether or not this object is a new object.
     */
    public function isNew( )
    {
	if ( $this->id == NULL )
	    return TRUE;

	return FALSE;
    }

    /**
     * Returns the DB id of this object.
     * @return The DB id of this object.
     */
    public function getId( )
    {
	return $this->id;
    }

    /**
     * Sets the DB id of this object.
     * @param $id The DB id of the object.
     * @return void
     */
    protected function setId( $id )
    {
	$this->id = $id;
    }

    /**
     * Causes this DbData object to save itself to the DB. If this object is new
     * an insertion will be performed, otherwise an update will be performed.
     * @param PDO $dbConnection The database connection this object will utilize to process
     * the save.
     * @return void
     * @throws Exception If an error is encountered in the process, an exception will be thrown.
     */
    public function save( PDO $dbConnection )
    {
	if ( $this->isNew( ) )
	    $this->insert( $dbConnection );
	else
	    $this->update( $dbConnection );
    }

    /**
     * This function performs an update of this object's data in the DB.
     * @param PDO $dbConnection The database connection this object will utilize to
     * process the update.
     * @return void
     * @throws Exception If an error is encountered in the process, an exception will be thrown.
     */
    abstract protected function update( PDO $dbConnection );

    /**
     * This function performs a new insertion of this object's data into the DB. It is the implementing
     * class's responsibilty to set the newly generated DB id after insertion, (this is due to synchronicity
     * dependencies which may arise from potential insertions of child objects). 
     * @param PDO $dbConnection The database connection this object will utilize to process
     * the insertion.
     * @return void
     * @throws Excetpion If an error is encountered in the process, an exception will be thrown.
     */
    abstract protected function insert( PDO $dbConnection );

    /**
     * Causes this DbData object to delete itself from the DB.
     * @param PDF $dbConnection The database conneciton this object will utilize to process
     * the deletion.
     * @return void
     * @throws Exception If an error is countered in the process, an exception will be thrown.
     */
    abstract public function delete( PDO $dbConnection );
}
?>