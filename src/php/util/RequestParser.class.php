<?php
ClassLoader::requireClassOnce( 'util/DbConnectionUtil' );

/**
 * This class provides a library of helper functions for parsing and filtering request parameters.
 * @author craigb
 */
class RequestParser
{
    // These constants define the predefined parameter filtering types
    const PARAM_FILTER_TYPE_INT = 0;
    const PARAM_FILTER_TYPE_ALPHA_ONLY = 1;
    const PARAM_FILTER_TYPE_ALPHA_NUMERIC = 2;
    const PARAM_FILTER_TYPE_EMAIL_ADDRESS = 3;
    const PARAM_FILTER_TYPE_ALPHA_WS_ONLY = 4;
    const PARAM_FILTER_TYPE_ALPHA_NUMERIC_WS_ONLY = 5;
    
    /**
     * Parses the specified request source for a parameter which corresponds to the specified param key.
     * The param filter type will be utilize to determine which pre-configured regex pattern will be utilized
     * to filter the parameter.
     * @param array $requestSource The source which the parameter data will be drawn from.
     * @param String $paramKey The key which will be used to retrieve the parameter data.
     * @param int $paramFilterType The predefined filter type which will be utilized to determine which
     * regex filter will be applied to filter the parameter value. If not specified, no filtering will occur.
     * @return The retrieved and filtered parameter value, if found. Otherwise NULL.
     */
    public static function parseRequestParam( array $requestSource, $paramKey, $paramFilterType = NULL )
    {
	if ( $paramFilterType == NULL )
	{
	    if ( isset( $requestSource[$paramKey] ) )
		return $requestSource[$paramKey];
	    else
		return NULL;
	}

	if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_INT )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[0-9]*$/' );
	else if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[a-zA-Z0-9]*$/' );
	else if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_ALPHA_ONLY )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[a-zA-Z]*$/' );
	else if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_ALPHA_WS_ONLY )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[a-zA-Z\s]*$/' );
	else if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC_WS_ONLY )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[a-zA-Z0-9\s]*$/' );
	else if ( $paramFilterType == RequestParser::PARAM_FILTER_TYPE_EMAIL_ADDRESS )
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey, '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/' );
	else
	    return RequestParser::parseRequestParamWithRegex( $requestSource, $paramKey );
    }

    /**
     * Parses the specified request source for a parameter which corresponds to the specified param key.
     * If specified, the regex pattern will be utilized to filter the parameter value.
     * @param array $requestSource The source which the request parameter will be drawn from.
     * @param String $paramKey The key which will be utilized to access the request parameter.
     * @param String $regexPattern If specified, will be utilized to filter the request parameter.
     * @return The retrieved and filtered request parameter, if found, otherwise NULL.
     */
    public static function parseRequestParamWithRegex( array $requestSource, $paramKey, $regexPattern = NULL )
    {
	// if the requested parameter key exists in the request source, proceed to parse its value
	if ( isset( $requestSource[$paramKey] ) )
	{
	    // retrieve the parameter value from the request source
	    $paramValue = $requestSource[$paramKey];
	    // if no regex pattern was specified, return the raw param value
	    if ( $regexPattern == NULL )
		return $paramValue;
	    // otherwise, filter the result via the regex expression
	    else
	    {
		$resultArray = array( );
		// if a value matched the regex expression, return the first match
		if ( preg_match( $regexPattern, $paramValue, $resultArray ) )
		    return $resultArray[0];
		// otherwise return null
		else
		    return NULL;
	    }
	}
	// otherwise return null
	else
	    return NULL;
    }
}

?>