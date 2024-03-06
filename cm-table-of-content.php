<?php
/*
Plugin Name: CM Table Of Contents
Plugin URI: https://www.cminds.com/wordpress-plugins-library/table-of-contents-plugin-for-wordpress/
Description: Adds the "table of contents" to the pages based on the h1-h6 or custom tags.
Version: 1.1.9
Author: CreativeMindsSolutions
Author URI: https://www.cminds.com/
*/

if( !ini_get('max_execution_time') || ini_get('max_execution_time') < 300 )
{
    /*
     * Setup the high max_execution_time to avoid timeouts during lenghty operations like importing big files
     */
    ini_set('max_execution_time', 300);
    set_time_limit(300);
}

/**
 * Define Plugin Version
 *
 * @since 1.0
 */
if( !defined('CMTOC_VERSION') )
{
    define('CMTOC_VERSION', '1.1.9');
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if( !defined('CMTOC_NAME') )
{
    define('CMTOC_NAME', 'CM Table Of Contents');
}

/**
 * Define Plugin canonical name
 *
 * @since 1.0
 */
if( !defined('CMTOC_CANONICAL_NAME') )
{
    define('CMTOC_CANONICAL_NAME', 'CM Table Of Contents');
}

/**
 * Define Plugin license name
 *
 * @since 1.0
 */
if( !defined('CMTOC_LICENSE_NAME') )
{
    define('CMTOC_LICENSE_NAME', 'CM Table Of Contents');
}

/**
 * Define Plugin File Name
 *
 * @since 1.0
 */
if( !defined('CMTOC_PLUGIN_FILE') )
{
    define('CMTOC_PLUGIN_FILE', __FILE__);
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if( !defined('CMTOC_RELEASE_NOTES') )
{
    define('CMTOC_RELEASE_NOTES', 'https://www.cminds.com/wordpress-plugins-library/table-of-contents-plugin-for-wordpress/');
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if( !defined('CMTOC_URL') )
{
    define('CMTOC_URL', 'https://www.cminds.com/wordpress-plugins-library/table-of-contents-plugin-for-wordpress/');
}

include_once plugin_dir_path( __FILE__ ) . 'tableOfContentsPro.php';
include_once plugin_dir_path( __FILE__ ) . 'tableOfContentsMetabox.php';

register_activation_hook( __FILE__ , array( 'CMTOC_Pro' , '_install' ) );
register_activation_hook( __FILE__ , array( 'CMTOC_Pro' , '_flush_rewrite_rules' ) );

CMTOC_Pro::init();
CMTOC_Metabox::init();
