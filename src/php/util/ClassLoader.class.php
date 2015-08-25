<?php
/**
 * This class encapsulates functionality for loading classes within this application.
 * @author craigb
 */
class ClassLoader
{
    private static $loadedClassPaths = array( );

    /**
     * This function attempts to require the class corresponding to the specified relative class path.
     * If the class has already been loaded, it will not load it again.
     * @param String $classPath The path of the class to be loaded.
     * @return void
     */
    public static function requireClassOnce( $classPath )
    {
	// if the specified class path hasn't already been loaded, attempt to load it
	if ( !isset( ClassLoader::$loadedClassPaths[$classPath] ) )
	{
	    $className = ClassLoader::parseClassName( $classPath );
	    ClassLoader::$loadedClassPaths[$classPath] = TRUE;
	    require( dirname( dirname( __FILE__ ) ) . "/$classPath.class.php" );	    
	}
    }

    /**
     * This function parses the class name from the specified class path.
     * @param String $classPath The class path to be parsed.
     * @return String The parsed class name.
     */
    public static function parseClassName( $classPath )
    {
	$pathTokens = explode( '/', $classPath );
	$className = $pathTokens[count($pathTokens)-1];
	return $className;
    }
}


?>
