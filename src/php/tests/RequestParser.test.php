<?php
require_once( dirname( dirname( __FILE__ ) ) . '/util/CLassLoader.class.php' );
ClassLoader::requireClassOnce( '/util/RequestParser' );

// test the predefined filter types
printf( "Testing predefined filter types...\n" );
$requestSource = array( );

// testing int retrieval
printf( "testing retrieving valid int value..." );
$requestSource['testInt'] = 2;
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testInt', RequestParser::PARAM_FILTER_TYPE_INT );
if ( $paramValue == NULL )
{
    printf( "FAILED: failed to retrieve param value.\n" );
    exit( -1 );
} 
if ( $paramValue != $requestSource['testInt'] )
{
    printf( "FAILED: retrieved value did not match input value: inputValue: {$requestSource['testInt']} retrievedValue: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

printf( "testing retrieving invalid int value..." );
$requestSource['testInt'] = '2bobskjflkjALKJSD';
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testInt', RequestParser::PARAM_FILTER_TYPE_INT );
if ( $paramValue != NULL )
{
    printf( "FAILED: expected NULL value, retrieved value: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

printf( "testing retrieving non existing value..." );
$paramValue = RequestParser::parseRequestParam( $requestSource, 'invalidKey', RequestParser::PARAM_FILTER_TYPE_INT );
if ( $paramValue != NULL )
{
    printf( "FAILED: expected NULL vlaue, retrieved value: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

printf( "testing retrieving valid alpha-only string..." );
$requestSource['testString'] = 'Bobby';
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testString', RequestParser::PARAM_FILTER_TYPE_ALPHA_ONLY );
if ( $paramValue == NULL )
{
    printf( "FAILED: failed to retrieve param value.\n" );
    exit( -1 );
}
if ( $paramValue != $requestSource['testString'] )
{
    printf( "FAILED: retrieved value did not match input value: inputValue: {$requestSource['testString']} retrievedValue: $paramValue\n" );
    exit( -1 );
} 
printf( "PASSED\n" );

printf( "testing retrieving invalid alpha-only string..." );
$requestSource['testString'] = '12323klsjdflkj';
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testString', RequestParser::PARAM_FILTER_TYPE_ALPHA_ONLY );
if ( $paramValue != NULL )
{
    printf( "FAILED: expected NULL value, retrieved value: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

printf( "testing retrieving valid alpha-numeric string..." );
$requestSource['testString'] = '12323klsjdflkASj23';
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testString', RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC );
if ( $paramValue == NULL )
{
    printf( "FAILED: failed to retrieve param value.\n" );
    exit( -1 );
}
if ( $paramValue != $requestSource['testString'] )
{
    printf( "FAILED: retrieved value did not match input value: inputValue: {$requestSource['testString']} retrievedValue: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

printf( "testing retrieving invalid alpha-numeric string..." );
$requestSource['testString'] = '12323klsjdflkASj23_+!@+@#';
$paramValue = RequestParser::parseRequestParam( $requestSource, 'testString', RequestParser::PARAM_FILTER_TYPE_ALPHA_NUMERIC );
if ( $paramValue != NULL )
{
    printf( "FAILED: expected NULL value, retrieved value: $paramValue\n" );
    exit( -1 );
}
printf( "PASSED\n" );

// TODO finish me
printf( "testing retrieving valid email address..." );
$requestSource['testEmail'] = 'bob.123_32KJD@gmail.com';
//$paramValue = 



// test a custom regex
// test without regex
?>