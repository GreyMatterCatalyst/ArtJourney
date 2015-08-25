<?php

/**
 * This class stores global settings for the entire application.
 * @author craigb
 */

class Settings
{
    private static $settingsMap = array (
	'DB_SCHEMA' => 'art_journey',
	'DB_USERNAME' => '<db_username>',
	'DB_PASSWORD' => '<db_password>',
	'DB_URL' => '<db_url>',
	'APPLICATION_URL' => '<application_url>',
	'DEBUG_MODE' => TRUE
	);

    /**
     * Returns the setting corresponding to the specified key.
     * @param $key The key for the requested setting.
     * @return The setting value corresponding to the specified key, or NULL if none exists.
     */
    public static function getSetting( $key )
    {
	if ( isset( Settings::$settingsMap[$key] ) )
	    return Settings::$settingsMap[$key];
	return NULL;
    }
}
?>