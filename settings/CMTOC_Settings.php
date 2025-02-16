<?php

namespace com\cminds\tableofcontents\settings;

include_once plugin_dir_path(__FILE__) . 'Settings.php';

class CMTOC_Settings extends Settings {

	protected static $abbrev = 'cmtoc';
	protected static $dir = __DIR__;
	protected static $settingsPageSlug;

	// Should be called on init hook
    public static function init() {

	    // Mandatory
        self::load_config();
	    add_action( 'admin_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
	    add_action(self::abbrev('_save_options_after_on_save'), [__CLASS__, 'addSuccessSaveMessage'], 10, 2);
        add_action(self::abbrev('_save_options_after'), [__CLASS__, 'addSuccessCleanupMessage'], 10, 2);
	    add_action('admin_menu', [__CLASS__, 'add_settings_page']);

	    // Optional
        add_action(self::abbrev('-settings-item-class'), [__CLASS__, 'settingItemClass'], 1, 2);
    }

	public static function add_settings_page() {
		add_menu_page( 'Table of Contents Options', CMTOC_NAME, 'manage_options', CMTOC_SETTINGS_OPTION, array( __CLASS__, 'render' ), CMTOC_PLUGIN_URL . 'assets/css/images/cm-toc-icon.png' );
		self::$settingsPageSlug = add_submenu_page( CMTOC_SETTINGS_OPTION, 'Table of Contents Options', 'Settings', 'manage_options', CMTOC_SETTINGS_OPTION, array( __CLASS__, 'render' ) );
	}
	
	public static function render() {
    	echo parent::render();
    	echo "<style>
        .full-width{
        width: 100% !important;
        }
        .full-width th{
        font-size: 18px;
        font-weight: bold;
        }
        .full-width .cm_field_help{
        right: 52%;
        font-size: 14px;
        }
        .disable-help-field .cm_field_help{
        display:none;
        }
        input.button.cmtoc-cleanup-button{
            background-color: #FFFFFF;
	        color: #6BC07F;
	        font-size: 14px;
	        line-height: 1;
	        padding: 10px 15px;
	        border-radius: 5px;
	        border-color: #6BC07F;
        }
        input.button.cmtoc-cleanup-button:hover{
            background-color: #E4E4E4;
	        color: #6BC07F;
	        border-color: #6BC07F;
        }
        div.cminds_settings_description a{
            color: #6BC07F;
        }
        div.cminds_settings_description{
        color:black;
        }
        .wrapper-label .field-label{
        width: 50%;
        padding-right: 10px;
        }
        .field-custom input{
        	transition: .3s;
	        width: 100%;
	        box-shadow: 0 0 3px 1px rgba(0, 0, 0, 0.25);
	        border-radius: 5px;
	        padding: 8px 10px;
	        line-height: 1;
        }
        .field-custom input:focus, .field-custom input:hover {
        	transition: .3s;
	        border-color: #6BC07F;
	        box-shadow: 0 0 0 1px #6BC07F;
	        outline: 2px solid transparent;
        }
        </style>";
	}

	// May be used for adding custom class to the option HTML container
	public static function settingItemClass($class, $setting) {
        if(stripos($setting, '_separator'))
            $class = 'full-width';
        else {
            if(!strpos($setting,'_back_to_top_')) {
                $arrayToCheck = ['Level', 'ShowHide', 'CurrentElement', 'Border', 'btn', 'floating_content'];
                foreach ($arrayToCheck as $item) {
                    if (stripos($setting, $item) && !stripos($setting, 'CurrentElementCustom'))
                        $class = 'disable-help-field';
                }
            }
        }
        return $class;
    }

	// Show success message after settings saving
	public static function addSuccessSaveMessage($post, $message) {
		$message[0] = __('Settings Saved', 'cm-table-of-content-table-of-content');
	}

    // Show success message after database cleanup
    public static function addSuccessCleanupMessage($post, $message) {
        if ( isset( $post[ 'cmtoc_table_of_contentsPluginCleanup' ] ) ) {
            self::_cleanup();
            $message[0] = CMTOC_NAME . ' data (options) have been removed from the database.';
        }
    }

    /**
     * Function cleans up the plugin, removing the terms, resetting the options etc.
     *
     * @return string
     */
    public static function _cleanup( $force = true ) {
        /*
         * Remove the data from the other tables
         */
        do_action( 'cmtoc_do_cleanup' );

        /*
         * Remove the options
         */
        $optionNames = wp_load_alloptions();

        $options_names = array_filter( array_keys( $optionNames ), function ( $k ) {
            return strpos( $k, 'cmtoc_' ) === 0;
        } );

        foreach ( $options_names as $optionName ) {
            delete_option( $optionName );
        }
    }

	// Put helper functions used in config.php
    public static function CustomPostTypesList() {
        $array = array();
        $args    = array(
            'public' => true,
        );

        $output   = 'objects';
        $operator = 'and';
        $post_types          = get_post_types( $args, $output, $operator );

        foreach ( $post_types as $post_type ) {
            $label = $post_type->labels->singular_name . ' (' . $post_type->name . ')';
            $name  = $post_type->name;
            $array[$name] = $label ;
        }
        return $array;
    }

    public static function renderFloatNumber($settings){
        return sprintf('<input type="number" name="%s" value="%s" min="0" max="1" step="%s" />', $settings['name'], $settings['value'],$settings['step'] );
    }
}
