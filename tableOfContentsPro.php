<?php
use com\cminds\tableofcontents\settings\CMTOC_Settings;

class TableOfContentException extends Exception {
	
};

class CMTOC_Pro {
	
	protected static $filePath = '';
	protected static $cssPath = '';
	protected static $jsPath = '';
	public static $lastQueryDetails = array();
	public static $calledClassName;

	const PAGE_YEARLY_OFFER = 'https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/';

	public static function init() {

		self::setupConstants();
		self::includeFiles();
		self::initFiles();

		if( empty(self::$calledClassName) ) {
			self::$calledClassName = __CLASS__;
		}

		$file = basename(__FILE__);
		$folder = basename(dirname(__FILE__));
		$hook = "in_plugin_update_message-{$folder}/{$file}";
		add_action($hook, array(self::$calledClassName, 'cmtoc_warn_on_upgrade'));

		self::$filePath = plugin_dir_url(__FILE__);
		self::$cssPath = self::$filePath . 'assets/css/';
		self::$jsPath = self::$filePath . 'assets/js/';

		add_action('admin_menu', array(self::$calledClassName, 'cmtoc_admin_menu'));
		add_action('admin_head', array(self::$calledClassName, 'addRicheditorButtons'));
		add_action('wp_loaded',['com\cminds\tableofcontents\settings\CMTOC_Settings','init']);
		add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmtoc_table_of_contents_admin_settings_scripts'));
		add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmtoc_table_of_contents_admin_edit_scripts'));

		add_action('wp_enqueue_scripts', array(__CLASS__, 'addScripts'));

		add_action('restrict_manage_posts', array(self::$calledClassName, 'cmtoc_restrict_manage_posts'));

		add_action('wp_print_styles', array(self::$calledClassName, 'cmtoc_table_of_contents_css'));
		add_action('admin_notices', array(self::$calledClassName, 'cmtoc_table_of_contents_admin_notice_wp33'));
		add_action('admin_notices', array(self::$calledClassName, 'cmtoc_table_of_contents_admin_notice_mbstring'));

		add_action('wp_ajax_cmtoc_get_table_of_content_backup', array(self::$calledClassName, 'cmtoc_table_of_contents_get_backup'));
		add_action('wp_ajax_nopriv_cmtoc_get_table_of_content_backup', array(self::$calledClassName, 'cmtoc_table_of_contents_get_backup'));

		add_filter('cmtoc_settings_table_of_content_tab_content_after', 'cminds_cmtoc_settings_table_of_content_tab_content_after');

			/*
			 * FILTERS
			 */
			add_filter('get_the_excerpt', array(self::$calledClassName, 'cmtoc_disable_parsing'), 1);
			add_filter('wpseo_opengraph_desc', array(self::$calledClassName, 'cmtoc_reenable_parsing'), 1);
			/*
			 * Make sure parser runs before the post or page content is outputted
			 */
			add_filter('the_content', array(self::$calledClassName, 'cmtoc_table_of_contents_parse'), 9999);

			add_filter('cmtoc_table_of_contents_parse_end', array(self::$calledClassName, 'outputTableOfContents'));

			/*
			 * It's a custom filter which can be applied to create the table-of-contents
			 */
			add_filter('cmtoc_table_of_contents_parse', array(self::$calledClassName, 'cmtoc_table_of_contents_parse'), 9999, 2);

			/*
			 * "Normal" Table of Contents Content
			 */
			add_filter('cmtoc_term_table_of_content_content', array(self::$calledClassName, 'cmtoc_table_of_contents_parse_strip_shortcodes'), 20);
	}

	/**
	 * Include the files
	 */
	public static function includeFiles() {
		do_action('cmtoc_include_files_before');
		include_once CMTOC_PLUGIN_DIR . 'functions.php';
		include_once CMTOC_PLUGIN_DIR . 'package/cminds-free.php';
		include_once CMTOC_PLUGIN_DIR . 'settings/CMTOC_Settings.php';
		do_action('cmtoc_include_files_after');
	}

	/**
	 * Initialize the files
	 */
	public static function initFiles() {
		do_action('cmtoc_init_files_before');
		do_action('cmtoc_init_files_after');
	}
	
	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.1
	 * @return void
	 */
	public static function setupConstants()
	{
		/**
		 * Define Plugin Directory
		 *
		 * @since 1.0
		 */
		if( !defined('CMTOC_PLUGIN_DIR') )
		{
			define('CMTOC_PLUGIN_DIR', plugin_dir_path(__FILE__));
		}

		/**
		 * Define Plugin URL
		 *
		 * @since 1.0
		 */
		if( !defined('CMTOC_PLUGIN_URL') )
		{
			define('CMTOC_PLUGIN_URL', plugin_dir_url(__FILE__));
		}

		/**
		 * Define Plugin Slug name
		 *
		 * @since 1.0
		 */
		if( !defined('CMTOC_SLUG_NAME') )
		{
			define('CMTOC_SLUG_NAME', 'cm-table-of-content-table-of-content');
		}

		/**
		 * Define Plugin basename
		 *
		 * @since 1.0
		 */
		if( !defined('CMTOC_PLUGIN') )
		{
			define('CMTOC_PLUGIN', plugin_basename(__FILE__));
		}

		if( !defined('CMTOC_MENU_OPTION') )
		{
			define('CMTOC_MENU_OPTION', 'cmtoc_menu_options');
		}

		define('CMTOC_ABOUT_OPTION', 'cmtoc_about');
		define('CMTOC_PRO_OPTION', 'cmtoc_pro');
		define('CMTOC_SETTINGS_OPTION', 'cmtoc_settings');

		do_action('cmtoc_setup_constants_after');
	}

	/**
	 * Adds the scripts which has to be included on the main glossary index page only
	 */
	public static function addScripts()
	{
		wp_enqueue_script( 'cmtoc-frontend-js', self::$jsPath . 'table-of-content-frontend.js', array( 'jquery' ) );
		
		$scriptData[ 'jump_back_btn' ] = (bool) CMTOC_Settings::get( 'cmtoc_jump_back_to_top', '0' );
		wp_localize_script('cmtoc-frontend-js', 'cmtoc_data', $scriptData);
	}

	public static function cmtoc_admin_menu()
	{
		global $submenu;
		$current_user = wp_get_current_user();

		//add_menu_page('Table of Contents Options', CMTOC_NAME, 'manage_options', CMTOC_SETTINGS_OPTION, array(self::$calledClassName, 'outputOptions'), CMTOC_PLUGIN_URL . 'assets/css/images/cm-toc-icon.png');
		//add_submenu_page(CMTOC_SETTINGS_OPTION, 'Table of Contents Options', 'Settings', 'manage_options', CMTOC_SETTINGS_OPTION, array(self::$calledClassName, 'outputOptions'));

		$tableOfContentItemsPerPage = get_user_meta(get_current_user_id(), 'edit_table_of_content_per_page', true);
		if( $tableOfContentItemsPerPage && intval($tableOfContentItemsPerPage) > 100 )
		{
			update_user_meta(get_current_user_id(), 'edit_table_of_content_per_page', 100);
		}

		add_filter('views_edit-table-of-content', array(self::$calledClassName, 'cmtoc_filter_admin_nav'), 10, 1);
	}

	public static function cmtoc_about()
	{
		ob_start();
		require 'views/backend/admin_about.php';
		$content = ob_get_contents();
		ob_end_clean();
		require 'views/backend/admin_template.php';
	}

	/**
	 * Shows pro page
	 */
	public static function cmtoc_admin_pro()
	{
		ob_start();
		include_once 'views/backend/admin_pro.php';
		$content = ob_get_contents();
		ob_end_clean();
		include_once 'views/backend/admin_template.php';
	}

	/**
	 * Function enqueues the scripts and styles for the admin Settings view
	 * @global type $parent_file
	 * @return type
	 */
	public static function cmtoc_table_of_contents_admin_settings_scripts()
	{
		global $parent_file;
		if( CMTOC_SETTINGS_OPTION !== $parent_file )
		{
			return;
		}

		wp_enqueue_style('jqueryUIStylesheet', self::$cssPath . 'jquery-ui-1.10.3.custom.css');
		wp_enqueue_style('table-of-content', self::$cssPath . 'table-of-content-backend.css');
		wp_enqueue_script('table-of-content-admin-js', self::$jsPath . 'table-of-content-admin.js', array('jquery'));

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-ui-tabs');

		$tableOfContentData['ajaxurl'] = admin_url('admin-ajax.php');
		wp_localize_script('table-of-content-admin-js', 'cmtoc_data', $tableOfContentData);
	}

	/**
	 * Function outputs the scripts and styles for the edit views
	 * @global type $typenow
	 * @return type
	 */
	public static function cmtoc_table_of_contents_admin_edit_scripts()
	{
		global $typenow;

		$defaultPostTypes = array('post', 'page');
		$allowedTermsBoxPostTypes = apply_filters('cmtoc_allowed_terms_metabox_posttypes', $defaultPostTypes);

		if( !in_array($typenow, $allowedTermsBoxPostTypes) )
		{
			return;
		}

		wp_enqueue_style('table-of-content', self::$cssPath . 'table-of-content-backend.css');
		wp_enqueue_script('table-of-content-admin-js', self::$jsPath . 'cm-table-of-content.js', array('jquery'));

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tooltip');
	}

	/**
	 * Filters admin navigation menus to show horizontal link bar
	 * @global string $submenu
	 * @global type $plugin_page
	 * @param type $views
	 * @return string
	 */
	public static function cmtoc_filter_admin_nav($views)
	{
		global $submenu, $plugin_page;
		$scheme = is_ssl() ? 'https://' : 'http://';
		$adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
		$currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);
		$submenus = array();
		if( isset($submenu[CMTOC_SETTINGS_OPTION]) )
		{
			$thisMenu = $submenu[CMTOC_SETTINGS_OPTION];

			$firstMenuItem = $thisMenu[0];
			unset($thisMenu[0]);

			$secondMenuItem = array('Trash', 'edit_posts', 'edit.php?post_status=trash&post_type=table-of-content', 'Trash');

			array_unshift($thisMenu, $firstMenuItem, $secondMenuItem);

			foreach($thisMenu as $item)
			{
				$slug = $item[2];
				$isCurrent = ($slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php'));
				$isExternalPage = strpos($item[2], 'http') !== FALSE;
				$isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== FALSE;
				$url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
				$target = $isExternalPage ? '_blank' : '';
				$submenus[$item[0]] = '<a href="' . $url . '" target="' . $target . '" class="' . ($isCurrent ? 'current' : '') . '">' . $item[0] . '</a>';
			}
		}
		return $submenus;
	}

	public static function cmtoc_restrict_manage_posts()
	{
		global $typenow;
		if( $typenow == 'table-of-content' )
		{
			$status = get_query_var('post_status');
			$options = apply_filters('cmtoc_table_of_contents_restrict_manage_posts', array('published' => 'Published', 'trash' => 'Trash'));

			echo '<select name="post_status">';
			foreach($options as $key => $label)
			{
				echo '<option value="' . $key . '" ' . selected($key, $status) . '>' . CMTOC_Pro::_e($label) . '</option>';
			}
			echo '</select>';
		}
	}

	/**
	 * Displays the horizontal navigation bar
	 * @global string $submenu
	 * @global type $plugin_page
	 */
	public static function cmtoc_showNav()
	{
		global $submenu, $plugin_page;
		$submenus = array();
		$scheme = is_ssl() ? 'https://' : 'http://';
		$adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
		$currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);

		if( isset($submenu[CMTOC_SETTINGS_OPTION]) )
		{
			$thisMenu = $submenu[CMTOC_SETTINGS_OPTION];
			foreach($thisMenu as $item)
			{
				$slug = $item[2];
				$isCurrent = ($slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php'));
				$isExternalPage = strpos($item[2], 'http') !== FALSE;
				$isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== FALSE;
				$url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
				$submenus[] = array(
					'link'    => $url,
					'title'   => $item[0],
					'current' => $isCurrent,
					'target'  => $isExternalPage ? '_blank' : ''
				);
			}
			require('views/backend/admin_nav.php');
		}
	}

	/**
	 * Add the dynamic CSS to reflect the styles set by the options
	 * @return type
	 */
	public static function cmtoc_table_of_contents_dynamic_css()
	{
		ob_start();
		echo apply_filters('cmtoc_dynamic_css_before', '');
		
		/*
		?>
		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_0 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size'); ?>;
		}

		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_1 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size'); ?>;
		}

		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_2 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size'); ?>;
		}

		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_3 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel3Size'); ?>;
		}

		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_4 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel4Size'); ?>;
		}

		.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_5 a {
			font-size: <?php echo CMTOC_Settings::get('cmtoc_table_of_contentsLevel5Size'); ?>;
		}
		
		<?php
		*/
		if( CMTOC_Settings::get('cmtoc_table_of_contentsDescriptionBorder') ) { ?>
			.cmtoc_table_of_contents_wrapper table.cmtoc_table_of_contents_table td.cmtoc_table_of_contents_description {
				border-top: 1px solid #DDD;
			}
			<?php
		}
		?>

		<?php
		echo apply_filters('cmtoc_dynamic_css_after', '');
		$content = ob_get_clean();
		return trim($content);
	}

	/**
	 * Outputs the frontend CSS
	 */
	public static function cmtoc_table_of_contents_css()
	{
		wp_enqueue_style('table-of-content', self::$cssPath . 'table-of-content-frontend.css');

		/*
		 * It's WP 3.3+ function
		 */
		if( function_exists('wp_add_inline_style') )
		{
			wp_add_inline_style('table-of-content', self::cmtoc_table_of_contents_dynamic_css());
		}
	}

	/**
	 * Adds a notice about wp version lower than required 3.3
	 * @global type $wp_version
	 */
	public static function cmtoc_table_of_contents_admin_notice_wp33()
	{
		global $wp_version;

		if( version_compare($wp_version, '3.3', '<') )
		{
			$message = sprintf(CMTOC_Pro::__('%s requires Wordpress version 3.3 or higher to work properly.'), CMTOC_NAME);
			cminds_show_message($message, true);
		}
	}

	/**
	 * Adds a notice about mbstring not being installed
	 * @global type $wp_version
	 */
	public static function cmtoc_table_of_contents_admin_notice_mbstring()
	{
		$mb_support = function_exists('mb_strtolower');

		if( !$mb_support )
		{
			$message = sprintf(CMTOC_Pro::__('%s since version 2.6.0 requires "mbstring" PHP extension to work! '), CMTOC_NAME);
			$message .= '<a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">(' . CMTOC_Pro::__('Installation instructions.') . ')</a>';
			cminds_show_message($message, true);
		}
	}

	/**
	 * Strips just one tag
	 * @param type $str
	 * @param type $tags
	 * @param type $stripContent
	 * @return type
	 */
	public static function cmtoc_strip_only($str, $tags, $stripContent = false)
	{
		$content = '';
		if( !is_array($tags) )
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
			if( end($tags) == '' )
			{
				array_pop($tags);
			}
		}
		foreach($tags as $tag)
		{
			if( $stripContent )
			{
				$content = '(.+</' . $tag . '[^>]*>|)';
			}
			$str = preg_replace('#</?' . $tag . '[^>]*>' . $content . '#is', '', $str);
		}
		return $str;
	}

	/**
	 * Disable the parsing for some reason
	 * @global type $wp_query
	 * @param type $smth
	 * @return type
	 */
	public static function cmtoc_disable_parsing($smth)
	{
		global $wp_query;
		if( $wp_query->is_main_query() && !$wp_query->is_singular )
		{  // to prevent conflict with Yost SEO
			remove_filter('the_content', array(self::$calledClassName, 'cmtoc_table_of_contents_parse'), 9999);
			do_action('cmtoc_disable_parsing');
		}
		return $smth;
	}

	/**
	 * Reenable the parsing for some reason
	 * @global type $wp_query
	 * @param type $smth
	 * @return type
	 */
	public static function cmtoc_reenable_parsing($smth)
	{
		add_filter('the_content', array(self::$calledClassName, 'cmtoc_table_of_contents_parse'), 9999);
		do_action('cmtoc_reenable_parsing');
		return $smth;
	}

	/**
	 * Function strips the shortcodes if the option is set
	 * @param type $content
	 * @return type
	 */
	public static function cmtoc_table_of_contents_parse_strip_shortcodes($content)
	{
		$content = do_shortcode($content);
		return $content;
	}

	/**
	 * Function returns TRUE if the given post should be parsed
	 * @param type $post
	 * @param type $force
	 * @return boolean
	 */
	public static function cmtoc_isParsingRequired($post, $force = false, $from_cache = false)
	{
		static $requiredAtLeastOnce = false;
		if( $from_cache )
		{
			/*
			 * Could be used to load JS/CSS in footer only when needed
			 */
			return $requiredAtLeastOnce;
		}

		/*
		 *  Skip parsing for excluded pages and posts (except table-of-content pages?! - Marcin)
		 */
		$parsingDisabled = get_post_meta($post->ID, '_table_of_content_disable_for_page', true) == 0;
		if( $parsingDisabled )
		{
			return FALSE;
		}

		if( $force )
		{
			return TRUE;
		}

		if( !is_object($post) )
		{
			return FALSE;
		}

		$currentPostType = get_post_type($post);
		$showOnPostTypes = array('post', 'page');
		$showOnHomepageAuthorpageEtc = (!is_page($post) && !is_single($post) && CMTOC_Settings::get('cmtoc_table_of_contentsOnlySingle') == 0);
		$onMainQueryOnly = TRUE;

		if( !is_array($showOnPostTypes) )
		{
			$showOnPostTypes = array();
		}
		$showOnSingleCustom = (is_singular($post) && in_array($currentPostType, $showOnPostTypes));

		$isTableOfContentPage = 'table-of-content' == $currentPostType;

		if( $isTableOfContentPage )
		{
			$condition = CMTOC_Settings::get('cmtoc_table_of_contentsOnTableOfContent');
		}
		else
		{
			$condition = ( $showOnHomepageAuthorpageEtc || $showOnSingleCustom );
		}

		$result = $onMainQueryOnly && $condition;
		if( $result )
		{
			$requiredAtLeastOnce = TRUE;
		}
		return $result;
	}

	/**
	 * Get's the custom key with the prefix and suffix
	 * @param type $key
	 * @return type
	 */
	public static function getCustomKey($key)
	{
		$customKey = !empty($key) ? '__' . $key . '__' : FALSE;
		return $customKey;
	}

	/**
	 * Prepare the data for the parser
	 *
	 * @global type $tableOfContentIndexArr
	 * @global type $tableOfContentSearchStringArr
	 * @global type $onlySynonyms
	 */
	public static function prepareParsingData()
	{
		static $runOnce = FALSE;

		if( $runOnce )
		{
			return;
		}

		$runOnce = TRUE;
	}

	public static function cmtoc_table_of_contents_parse($content, $force = false)
	{
		global $post, $wp_query;

		if( $post === NULL )
		{
			return $content;
		}

		if( !is_object($post) )
		{
			$post = $wp_query->post;
		}

		$seo = doing_action('wpseo_opengraph');
		if( $seo )
		{
			return $content;
		}

		$runParser = self::cmtoc_isParsingRequired($post, $force);
		
		if( !$runParser )
		{
			/*
			 * Returns empty string
			 */
			// add_shortcode('cmtoc_table_of_contents', '__return_empty_string');
			$removeShortcodeContent = do_shortcode($content);
			return $removeShortcodeContent;
		}

		add_shortcode( 'cmtoc_table_of_contents', array( self::$calledClassName, 'displayTableOfContentsShortcode' ) );

		/*
		 * Run the table-of-content parser
		 */
		$contentHash = 'cmtoc_content' . sha1($post->ID);
		if( !$force )
		{
			if( !CMTOC_Settings::get('cmtoc_table_of_contentsEnableCaching', FALSE) )
			{
				delete_transient($contentHash);
			}
			$result = get_transient($contentHash);
			if( $result !== false )
			{
				return $result;
			}
		}

		/*
		 * Prepare the parsing data
		 */
		self::prepareParsingData();

		$excludeTableOfContent_regex = '/\\['                              // Opening bracket
				. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
				. "(table_of_content_exclude)"                     // 2: Shortcode name
				. '\\b'                              // Word boundary
				. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
				. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
				. '(?:'
				. '\\/(?!\\])'               // A forward slash not followed by a closing bracket
				. '[^\\]\\/]*'               // Not a closing bracket or forward slash
				. ')*?'
				. ')'
				. '(?:'
				. '(\\/)'                        // 4: Self closing tag ...
				. '\\]'                          // ... and closing bracket
				. '|'
				. '\\]'                          // Closing bracket
				. '(?:'
				. '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
				. '[^\\[]*+'             // Not an opening bracket
				. '(?:'
				. '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
				. '[^\\[]*+'         // Not an opening bracket
				. ')*+'
				. ')'
				. '\\[\\/\\2\\]'             // Closing shortcode tag
				. ')?'
				. ')'
				. '(\\]?)/s';

		$excludeTableOfContentStrs = array();

		/*
		 * Replace exclude tags and content between them in purpose to save the original text as is
		 * before table-of-content plug go over the content and add its code
		 * (later will be returned to the marked places in content)
		 */
		$excludeTagsCount = preg_match_all($excludeTableOfContent_regex, $content, $excludeTableOfContentStrs, PREG_PATTERN_ORDER);
		$i = 0;

		if( $excludeTagsCount > 0 )
		{
			foreach($excludeTableOfContentStrs[0] as $excludeStr)
			{
				$content = preg_replace($excludeTableOfContent_regex, '#' . $i . 'excludeTableOfContent', $content, 1);
				$i++;
			}
		}

		/*
		 * Get the list of selectors - either h1...h6 or custom for the page
		 */
		$listOfSelectors = self::getListOfSelectors();

		$content = self::cmtoc_dom_str_replace($content, $listOfSelectors);

		if( $excludeTagsCount > 0 )
		{
			$i = 0;
			foreach($excludeTableOfContentStrs[0] as $excludeStr)
			{
				$content = str_replace('#' . $i . 'excludeTableOfContent', $excludeStr, $content);
				$i++;
			}
			//remove all the exclude signs
			$content = str_replace(array('[table_of_content_exclude]', '[/table_of_content_exclude]'), array('', ''), $content);
		}

		$content = apply_filters('cmtoc_table_of_contents_parse_end', $content);

		if( CMTOC_Settings::get('cmtoc_table_of_contentsEnableCaching', FALSE) )
		{
			$result = set_transient($contentHash, $content, 1 * MINUTE_IN_SECONDS);
		}

		return $content;
	}

	/**
	 * Returns the list of Selectors
	 * @return array
	 */
	public static function getListOfSelectors()
	{
		global $post, $wp_query;

		if( !is_object($post) )
		{
			$post = $wp_query->post;
		}

		if( !empty($post) )
		{
			$useCustomSelectors = get_post_meta($post->ID, '_cmtoc_use_custom_selectors', true);
		}

		if( !empty($post) && $useCustomSelectors )
		{
			$listOfSelectors = get_post_meta($post->ID, '_cmtoc_custom_selectors', true);
		}
		else
		{
			for($level = 0; $level < 6; $level++)
			{
				$selectorArr['tag'] = CMTOC_Settings::get('cmtoc_table_of_contentsLevel' . $level . 'Tag');
				$selectorArr['class'] = CMTOC_Settings::get('cmtoc_table_of_contentsLevel' . $level . 'Class');
				$selectorArr['id'] = CMTOC_Settings::get('cmtoc_table_of_contentsLevel' . $level . 'Id');

				$listOfSelectors[] = array_filter($selectorArr);
			}
		}

		return $listOfSelectors;
	}

	public static function getTableOfContentsContent()
	{
		global $cmtoc_FoundItems;

		$tableOfContentsContent = '';

		$tableOfContentDisplayHeaders = CMTOC_Pro::__(CMTOC_Settings::get('cmtoc_table_of_contentsHeaderDescription', 'Table Of Contents'));

		if( !empty($cmtoc_FoundItems) )
		{
			
			$listStyleType = CMTOC_Settings::get( 'cmtoc_table_of_contentsListType', 'none' );
			
			$listStyleTypeClass = '';
			if ( 'decimal-indent' === $listStyleType ) {
				$listStyleTypeClass = 'cmtoc_table';
			}
		
			$tableOfContentsContent .= '<div class="cmtoc_table_of_contents_wrapper">';
			if( !empty($tableOfContentDisplayHeaders) )
			{
				$tableOfContentsContent .= '<div class="cmtoc_table_of_contents_description">' . $tableOfContentDisplayHeaders . '</div>';
			}

			$tableOfContentsContent .= '<ul class="cmtoc_table_of_contents_table">';

			/*
			foreach($cmtoc_FoundItems as $tableOfContentKey => $tableOfContentArr)
			{
				$levelClass = 'cmtoc_level_' . esc_attr($tableOfContentArr['level']);

				$tableOfContentIndexHref = esc_attr($tableOfContentArr['href']);
				$tableOfContentItemContent = apply_filters('cmtoc_term_table_of_content_content', esc_attr($tableOfContentArr['text']));
				$tableOfContentId = 'cmtoc_table_of_contents_' . esc_attr($tableOfContentArr['index']);

				$tableOfContentsContent .= '<li id="' . $tableOfContentId . '" class="cmtoc_table_of_contents_row ' . $levelClass . '">';
				$tableOfContentsContent .= '<a href="#' . $tableOfContentIndexHref . '">';
				$tableOfContentsContent .= $tableOfContentItemContent;
				$tableOfContentsContent .= '</a>';
				$tableOfContentsContent .= '</li>';
				
			}
			*/
			
			$gaps = array();

            foreach ($cmtoc_FoundItems as $toc_key => $toc_item) {

                $current_level = (int)esc_attr($toc_item['level']);

                $toc_item_url = get_permalink($current_object);
                if (null !== $toc_item['page'] && null === $widgetpost) {
                    if (is_numeric($toc_item['page'])) {
                        $toc_item_url = self::_internalLinkPages(($toc_item['page'] + 1));
                    } else {
                        $toc_item_url = $toc_item['page'];
                    }
                }

                if (!empty($toc_item['base'])) {
                    $toc_item_url = $toc_item['base'];
                }

                $toc_item_href = $toc_item_url;
                if (!empty($toc_item['href'])) {
                    $toc_item_href .= '#' . esc_attr($toc_item['href']);
                }
                $toc_item_content = apply_filters('cmtoc_term_table_of_content_content', esc_attr($toc_item['text']));
                $toc_item_id = 'cmtoc_table_of_contents_' . esc_attr($toc_item['index']);
                $element_class ='cmtoc_table_of_contents_row';
                if (0 == $current_level ) {
                    if (0 !== (int)$toc_key) {
                        while ($depth > 0) {
                            $tableOfContentsContent .= '</li></ul>';
                            $depth--;
                        }
                    }
                    $element_class = 'cmtoc_table_of_contents_row_wrapper';
                } elseif ((int)$prev_level < (int)$current_level) {
                    if (-1 != (int)$prev_level) {
                        $gaps[] = $current_level - $prev_level; // gap is distance btw parent and child items level
                        //e.g. prnt is H2, chld is H4, gap = 4-2 = 2
                    }
                    $tableOfContentsContent .= "<ul class='cmtoc_table_of_contents_sublevel_wrapper'>";
                    $depth++;
                } elseif ((int)$prev_level > (int)$current_level) {
                    $gap = (int)array_pop($gaps);
                    $tableOfContentsContent .= "</li></ul>";
                    $depth--;
                    while (($prev_level - $gap > $current_level) && !empty($gaps)) { // && $depth > 1
                        $tableOfContentsContent .= "</li></ul>";
                        $gap += array_pop($gaps);
                        $depth--;
                    };
                    $tableOfContentsContent .= "</li>";
                }
                if ((int)$prev_level == (int)$current_level) {
                    $tableOfContentsContent .= "</li>";
                }

                $tableOfContentsContent .= '<li id="' . $toc_item_id . '" class="'. $element_class. ' cmtoc_level_' . $current_level . '">';
                $noFollow = CMTOC_Settings::get('cmtoc_table_of_contentsNoFollow') ? ' rel="nofollow"' : '';
                $tableOfContentsContent .= '<a href="' . $toc_item_href . '"' . $noFollow . '>' . $toc_item_content . '</a>';

                if ((int)array_key_last($cmtoc_FoundItems) == (int)$toc_key) {
                    while ($depth > 0) {
                        $tableOfContentsContent .= '</li></ul>';
                        $depth--;
                    }
                }
                $prev_level = $current_level;
            } // end of foreach()
            // CLose TOC element			
			
			$tableOfContentsContent .= '</ul>';
			$tableOfContentsContent .= '</div>';
			
			$tableOfContentsContent .= '<style>';
			$tableOfContentsContent .= 'ul.cmtoc_table_of_contents_table * { list-style-type:'.CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').' !important; }';
			
			if ( 'decimal-indent' === $listStyleType ) {
				$tableOfContentsContent .= 'ul.cmtoc_table_of_contents_table ul, ul.cmtoc_table_of_contents_table { list-style-type:none; counter-reset:item; margin:0; padding:0; }';
				$tableOfContentsContent .= 'ul.cmtoc_table_of_contents_table ul > li, ul.cmtoc_table_of_contents_table > li { display:table; counter-increment:item; margin-bottom:0.3em; }';
				$tableOfContentsContent .= 'ul.cmtoc_table_of_contents_table ul > li:before, ul.cmtoc_table_of_contents_table >li:before { content:counters(item, ".") ". "; }';
				$tableOfContentsContent .= 'ul.cmtoc_table_of_contents_table li ul > li:before { content:counters(item, ".") " "; }';
			}
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsBorderWidth', '0px'),'px')) {
				$contentsBorderWidth = CMTOC_Settings::get('cmtoc_table_of_contentsBorderWidth', '0px');
			} else {
				$contentsBorderWidth = CMTOC_Settings::get('cmtoc_table_of_contentsBorderWidth', '0px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper {
				background-color: '.CMTOC_Settings::get('cmtoc_table_of_contentsBackgroundColor', '#fff').';
				border-width: '.$contentsBorderWidth.';
				border-color: '.CMTOC_Settings::get('cmtoc_table_of_contentsBorderColor', '#373737').';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px'),'px')) {
				$contentsLevel0Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px');
			} else {
				$contentsLevel0Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_0 a {
				font-size: '.$contentsLevel0Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel0Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22px'),'px')) {
				$contentsLevel1Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22px');
			} else {
				$contentsLevel1Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_1 a {
				font-size: '.$contentsLevel1Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel1Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19px'),'px')) {
				$contentsLevel2Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19px');
			} else {
				$contentsLevel2Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_2 a {
				font-size: '.$contentsLevel2Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel2Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel3Size', '19px'),'px')) {
				$contentsLevel3Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel3Size', '19px');
			} else {
				$contentsLevel3Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel3Size', '19px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_3 a {
				font-size: '.$contentsLevel3Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel3Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel4Size', '19px'),'px')) {
				$contentsLevel4Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel4Size', '19px');
			} else {
				$contentsLevel4Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel4Size', '19px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_4 a {
				font-size: '.$contentsLevel4Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel4Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_table_of_contentsLevel5Size', '19px'),'px')) {
				$contentsLevel5Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel5Size', '19px');
			} else {
				$contentsLevel5Size = CMTOC_Settings::get('cmtoc_table_of_contentsLevel5Size', '19px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_table_of_contents_wrapper ul.cmtoc_table_of_contents_table li.cmtoc_level_5 a {
				font-size: '.$contentsLevel5Size.';
				color: '.CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel5Color', '#373737' ).';
			}';
			
			if(strpos(CMTOC_Settings::get('cmtoc_back_to_top_border_width', '1px'),'px')) {
				$back_to_top_border_width = CMTOC_Settings::get('cmtoc_back_to_top_border_width', '1px');
			} else {
				$back_to_top_border_width = CMTOC_Settings::get('cmtoc_back_to_top_border_width', '1px').'px';
			}
			
			$tableOfContentsContent .= '.cmtoc_to_top_btn {
				background-color: '.CMTOC_Settings::get('cmtoc_back_to_top_background', '#fff').';
				border-width: '.$back_to_top_border_width.';
				border-color: '.CMTOC_Settings::get('cmtoc_back_to_top_border_color', '#cecece').';
				width: '.CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px').';
				height: '.CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px').';
				fill: '.CMTOC_Settings::get('cmtoc_back_to_top_arrow_color', '#111').';
			}';
			
			$tableOfContentsContent .= '.cmtoc_to_top_btn #cmtoc_svg-arrow {
				width: '.CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px').';
				height: '.CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px').';
			}';
			
			$tableOfContentsContent .= '</style>';
			
		}

		return $tableOfContentsContent;
	}
	
	private static function _internalLinkPages( $i ) {
        global $wp_rewrite;
        $post = get_post();
        $permalink = get_permalink();
        if(self::isDiviLayout($post)){
            $permalink = get_site_url() . $_SERVER['REQUEST_URI'];
        }

        if ( 1 == $i ) {
            $url = $permalink;
        } else {
            if ( '' == get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ) ) ) {
                $url = add_query_arg( 'page', $i, $permalink );
            } elseif ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
                $url = trailingslashit( $permalink ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
            } else {
                $url = trailingslashit( $permalink ) . user_trailingslashit( $i, 'single_paged' );
            }
        }
        return $url;
    }
	
	/**
	 * Returns TRUE if the shortcode was found
	 * @staticvar boolean $found
	 * @param type $setFound
	 * @return type
	 */
	public static function wasShortcodeFound($setFound = FALSE)
	{
		static $found = FALSE;
		if( $setFound )
		{
			$found = $setFound;
		}
		return $found;
	}

	/**
	 * Display the table-of-contents
	 * @param type $atts
	 * @param type $text
	 * @return type
	 */
	public static function displayTableOfContentsShortcode($atts = array(), $text = '')
	{
		$tableOfContentsContent = '';
        
		/*
		 * Only show once
		 */
		if( !self::wasShortcodeFound() )
		{
			$tableOfContentsContent = self::getTableOfContentsContent();
			self::wasShortcodeFound(TRUE);
		}

		return $tableOfContentsContent;
	}

	/**
	 * Outputs the table of content
	 * @param type $content
	 * @return string
	 */

	public static function outputTableOfContents( $content ) {
        global $post;
        $contentWithTableOfContents = do_shortcode( $content );

        $showAutomaticallyDisabled = (bool) get_post_meta( $post->ID, '_table_of_content_auto_shortcode_disable', true );
        $shortcodeWasFound         = self::wasShortcodeFound();
        if ( !$shortcodeWasFound && !$showAutomaticallyDisabled ) {
            $tableOfContentsContent = self::getTableOfContentsContent();
//			self::wasShortcodeFound( TRUE );

            $contentWithTableOfContents = $tableOfContentsContent . $contentWithTableOfContents;
        }

        return $contentWithTableOfContents;
    }

	/**
	 * Function responsible for saving the options
	 */
	public static function saveOptions()
	{
		$messages = '';
		$_POST = array_map('stripslashes_deep', $_POST);
		$post = $_POST;
		if(!isset($post['_wpnonce']) || (isset($post['_wpnonce']) && !wp_verify_nonce($post['_wpnonce'],'update-options'))){
		    return array('messages' => $messages);
        }

		if( isset($post["cmtoc_table_of_contentSave"]) )
		{
			do_action('cmtoc_save_options_before', $post, $messages);
			$enqueeFlushRules = false;
			/*
			 * Update the page options
			 */

			if( apply_filters('cmtoc_enqueueFlushRules', $enqueeFlushRules, $post) )
			{
				self::_flush_rewrite_rules();
			}

			unset($post['cmtoc_table_of_contentsSave']);

			function cmtoc_get_the_option_names($k)
			{
				return strpos($k, 'cmtoc_') === 0;
			}

			$options_names = apply_filters('cmtoc_thirdparty_option_names', array_filter(array_keys($post), 'cmtoc_get_the_option_names'));

			foreach($options_names as $option_name)
			{
				if( !isset($post[$option_name]) )
				{
					update_option($option_name, 0);
				}
				else
				{
					$optionValue = is_array($post[$option_name]) ? $post[$option_name] : sanitize_text_field(trim($post[$option_name]));
					update_option($option_name, $optionValue);
				}
			}
			do_action('cmtoc_save_options_after_on_save', $post, array(&$messages));
		}

		do_action('cmtoc_save_options_after', $post, array(&$messages));

		if( isset($post['cmtoc_table_of_contentsPluginCleanup']) )
		{
			self::_cleanup();
			$messages = CMTOC_NAME . ' data (terms, options) have been removed from the database.';
		}

		return array('messages' => $messages);
	}

	/**
	 * Displays the options screen
	 */
	public static function outputOptions()
	{
		$result = self::saveOptions();
		$messages = $result['messages'];

		ob_start();
		require('views/backend/admin_settings.php');
		$content = ob_get_contents();
		ob_end_clean();
		require('views/backend/admin_template.php');
	}

	/**
	 * Outputs the Affiliate Referral Snippet
	 * @return type
	 */
	public static function cmtoc_getReferralSnippet()
	{
		ob_start();
		?>
		<span class="table_of_content_referral_link">
			<a target="_blank" href="<?php echo CMTOC_URL; ?>?af=<?php echo CMTOC_Settings::get('cmtoc_table_of_contentsAffiliateCode') ?>">
				<img src="https://www.cminds.com/wp-content/uploads/download_table_of_content.png" width=122 height=22 alt="Download Table Of Contents" title="Download Table Of Contents" />
			</a>
		</span>
		<?php
		$referralSnippet = ob_get_clean();
		return $referralSnippet;
	}

	/**
	 * Attaches the hooks adding the custom buttons to TinyMCE and CKeditor
	 * @return type
	 */
	public static function addRicheditorButtons()
	{
		/*
		 *  check user permissions
		 */
		if( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
		{
			return;
		}

		// check if WYSIWYG is enabled
		if( 'true' == get_user_option('rich_editing') )
		{
			add_filter('mce_external_plugins', array(self::$calledClassName, 'cmtoc_mcePlugin'));
			add_filter('mce_buttons', array(self::$calledClassName, 'cmtoc_mceButtons'));

			add_filter('ckeditor_external_plugins', array(self::$calledClassName, 'cmtoc_ckeditorPlugin'));
			add_filter('ckeditor_buttons', array(self::$calledClassName, 'cmtoc_ckeditorButtons'));
		}
	}

	public static function cmtoc_mcePlugin($plugins)
	{
		$plugins = (array) $plugins;
		$plugins['cmtoc_table_of_contents'] = self::$jsPath . 'editor/table-of-contents-mce.js';
		return $plugins;
	}

	public static function cmtoc_mceButtons($buttons)
	{
		array_push($buttons, '|', 'cmtoc_exclude', 'cmtoc_parse');
		return $buttons;
	}

	public static function cmtoc_ckeditorPlugin($plugins)
	{
		$plugins = (array) $plugins;
		$plugins['cmtoc_table_of_contents'] = self::$jsPath . '/editor/ckeditor/plugin.js';
		return $plugins;
	}

	public static function cmtoc_ckeditorButtons($buttons)
	{
		array_push($buttons, 'cmtoc_exclude', 'cmtoc_parse');
		return $buttons;
	}

	public static function cmtoc_warn_on_upgrade()
	{
		?>
		<div style="margin-top: 1em"><span style="color: red; font-size: larger">STOP!</span> Do <em>not</em> click &quot;update automatically&quot; as you will be <em>downgraded</em> to the free version of Table of Contents. Instead, download the Pro update directly from <a href="<?php echo CMTOC_URL; ?>"><?php echo CMTOC_URL; ?></a>.</div>
		<div style="font-size: smaller">Table Of Contents Pro does not use WordPress's standard update mechanism. We apologize for the inconvenience!</div>
		<?php
	}

	/**
	 * New function to search the terms in the content
	 *
	 * @param strin $html
	 * @param string $tableOfContentSearchString
	 * @since 2.3.1
	 * @return type
	 */
	 public static function cmtoc_dom_str_replace($html, $listOfSelectors)
 	{
 		static $tableOfContentItemsCounter = 0;
 		global $cmWrapItUp, $cmtoc_FoundItems;

 		$tableOfContentsIdBase = 'cmtoc_anchor_id_';

 		if( !empty($html) && is_string($html) )
 		{
 			if( $cmWrapItUp )
 			{
 				$html = '<span>' . $html . '</span>';
 			}
 			$dom = new DOMDocument();
 			/*
 			 * loadXml needs properly formatted documents, so it's better to use loadHtml, but it needs a hack to properly handle UTF-8 encoding
 			 */
 			libxml_use_internal_errors(true);
 			if( !$dom->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8")) )
 			{
 				libxml_clear_errors();
 			}
 			$xpath = new DOMXPath($dom);
			
			$queryArr = array();

 			foreach($listOfSelectors as $selectorArray)
 			{
 				if( empty($selectorArray['tag']) )
 				{
 					continue;
 				}

 				$tag = $selectorArray['tag'];

 				$class = '';
 				if( !empty($selectorArray['class']) )
 				{
 					$class = '[contains(concat(\' \', normalize-space(@class), \' \'), \' ' . $selectorArray['class'] . ' \')]';
 				}
                $id = '';
                if ( !empty( $selectorArray[ 'id' ] ) ) {
                    $id = '[contains(concat(\' \', normalize-space(@id), \' \'), \' ' . $selectorArray[ 'id' ] . ' \')]';
                }
 				$queryArr[] = '//' . $tag . $class . $id;
 			}

 			$query = implode('|', $queryArr);

 			foreach($xpath->query($query) as $node)
 			{
 				$tableOfContentsNewId = $tableOfContentsIdBase . $tableOfContentItemsCounter;
 				/* @var $node DOMText */
 				$replaced = '<i id="' . $tableOfContentsNewId . '" class="cmtoc_invisible_anchor"></i>';

 				$itemAttributes = array();
 				// $parentNode = $node->parentNode;
 				// $itemAttributes['tag'] = $parentNode->nodeName;
 				// $itemAttributes['text'] = $node->wholeText;
 				// if( $parentNode->hasAttributes() )
 				// {
 				// 	foreach($parentNode->attributes as $attribute_name => $attribute_node)
 				// 	{
 				// 		$itemAttributes[$attribute_name] = $attribute_node->nodeValue;
 				// 	}
 				// }

 				$itemAttributes[ 'tag' ]  = $node->nodeName;
         $itemAttributes[ 'text' ] = $node->textContent;
 				if( $node->hasAttributes() ){
 					foreach($node->attributes as $attribute_name => $attribute_node)
 					{
 						$itemAttributes[$attribute_name] = $attribute_node->nodeValue;
 					}
 				}
 				$tableOfContentsItem = array(
 					'level' => self::getItemLevel($itemAttributes),
 					'href'  => $tableOfContentsNewId,
 					'index' => $tableOfContentItemsCounter,
 					'text'  => $itemAttributes['text'],
 				);

 				/*
 				 * Increment the counter
 				 */
 				$tableOfContentItemsCounter++;
 				$cmtoc_FoundItems[] = $tableOfContentsItem;

 				if( !empty($replaced) )
 				{
 					$newNode = $dom->createDocumentFragment();
 					$replacedShortcodes = strip_shortcodes($replaced);
 					$result = $newNode->appendXML('<![CDATA[' . $replacedShortcodes . ']]>');

 					if( $result !== false )
 					{
 						// $node->parentNode->replaceChild($newNode, $node);
 							$node->appendChild( $newNode);
 					}
 				}
 			}

 			/*
 			 *  get only the body tag with its contents, then trim the body tag itself to get only the original content
 			 */
 			$bodyNode = $xpath->query('//body')->item(0);

 			if( $bodyNode !== NULL )
 			{
 				$newDom = new DOMDocument();
 				$newDom->appendChild($newDom->importNode($bodyNode, TRUE));

 				$intermalHtml = $newDom->saveHTML();
 				$html = mb_substr(trim($intermalHtml), 6, (mb_strlen($intermalHtml) - 14), "UTF-8");
 				/*
 				 * Fixing the self-closing which is lost due to a bug in DOMDocument->saveHtml() (caused a conflict with NextGen)
 				 */
 				$html = preg_replace('#(<img[^>]*[^/])>#Ui', '$1/>', $html);
 			}
 		}

 		if( $cmWrapItUp )
 		{
 			$html = mb_substr(trim($html), 6, (mb_strlen($html) - 13), "UTF-8");
 		}

 		return $html;
 	}

	/**
	 * Returns the level of the item
	 * @param array $itemAttributes
	 * @return int
	 */
	public static function getItemLevel($itemAttributes)
	{
		$listOfSelectors = self::getListOfSelectors();
		foreach($listOfSelectors as $level => $selectorAttributes)
		{
			$matches['tag'] = strtolower($itemAttributes['tag']) == strtolower($selectorAttributes['tag']);
			if( !$matches['tag'] )
			{
				continue;
			}

			if( isset($selectorAttributes['class']) && isset($itemAttributes['class']) )
			{
				$matches['class'] = strpos(strtolower($itemAttributes['class']), strtolower($selectorAttributes['class'])) !== FALSE;
				if( !$matches['class'] )
				{
					continue;
				}
			}

            if( isset($selectorAttributes['id'])/* && isset($itemAttributes['id']) */)
			{
				$matches['id'] = strtolower($itemAttributes['id']) == strtolower($selectorAttributes['id']);
				if( !$matches['id'] )
				{
					continue;
				}
			}
			return $level;
		}
	}

	/**
	 * Function renders (default) or returns the setttings tabs
	 *
	 * @param type $return
	 * @return string
	 */
	public static function renderSettingsTabs($return = false)
	{
		$content = '';
		$settingsTabsArrayBase = array();

		$settingsTabsArray = apply_filters('cmtoc-settings-tabs-array', $settingsTabsArrayBase);

		if( $settingsTabsArray )
		{
			foreach($settingsTabsArray as $tabKey => $tabLabel)
			{
				$filterName = 'cmtoc-custom-settings-tab-content-' . $tabKey;

				$content .= '<div id="tabs-' . $tabKey . '">';
				$tabContent = apply_filters($filterName, '');
				$content .= $tabContent;
				$content .= '</div>';
			}
		}

		if( $return )
		{
			return $content;
		}
		echo $content;
	}

	/**
	 * Function renders (default) or returns the setttings tabs
	 *
	 * @param type $return
	 * @return string
	 */
	public static function renderSettingsTabsControls($return = false)
	{
		$content = '';
		$settingsTabsArrayBase = array(
			'55' => 'Upgrade',
			'1' => 'Installation Instructions',
			'2'  => 'General Settings',
			'3'  => 'Table of Contents',
		);

		$settingsTabsArray = apply_filters('cmtoc-settings-tabs-array', $settingsTabsArrayBase);

		ksort($settingsTabsArray);

		if( $settingsTabsArray )
		{
			$content .= '<ul>';
			foreach($settingsTabsArray as $tabKey => $tabLabel)
			{
				$content .= '<li><a href="#tabs-' . $tabKey . '">' . $tabLabel . '</a></li>';
			}
			$content .= '</ul>';
		}

		if( $return )
		{
			return $content;
		}
		echo $content;
	}

	public static function outputCustomPostTypesList()
	{
		$content = '';
		$args = array(
			'public' => true,
//            '_builtin' => false
		);

		$output = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'

		$post_types = get_post_types($args, $output, $operator);
		$selected_post_types = array('post', 'page');

		if( !is_array($selected_post_types) )
		{
			$selected_post_types = array();
		}

		foreach($post_types as $post_type)
		{
			$label = $post_type->labels->singular_name . ' (' . $post_type->name . ')';
			$name = $post_type->name;

			$content .= '<div><label><input type="checkbox" name="cmtoc_table_of_contentsOnPosttypes[]" ' . checked(true, in_array($name, $selected_post_types), false) . ' value="' . $name . '" />' . $label . '</label></div>';
		}
		return $content;
	}

	/**
	 * Function cleans up the plugin, removing the terms, resetting the options etc.
	 *
	 * @return string
	 */
	protected static function _cleanup($force = true)
	{
		/*
		 * Remove the data from the other tables
		 */
		do_action('cmtoc_do_cleanup');

		/*
		 * Remove the options
		 */
		$optionNames = wp_load_alloptions();

		function cmtoc_get_the_option_names($k)
		{
			return strpos($k, 'cmtoc_') === 0;
		}

		$options_names = array_filter(array_keys($optionNames), 'cmtoc_get_the_option_names');
		foreach($options_names as $optionName)
		{
			delete_option($optionName);
		}
	}

	/**
	 * Plugin activation
	 */
	protected static function _activate()
	{
		do_action('cmtoc_do_activate');
	}

	/**
	 * Plugin installation
	 *
	 * @global type $wpdb
	 * @param type $networkwide
	 * @return type
	 */
	public static function _install($networkwide)
	{
		global $wpdb;

		if( function_exists('is_multisite') && is_multisite() )
		{
			// check if it is a network activation - if so, run the activation function for each blog id
			if( $networkwide )
			{
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs}"));
				foreach($blogids as $blog_id)
				{
					switch_to_blog($blog_id);
					self::_activate();
				}
				switch_to_blog($old_blog);
				return;
			}
		}

		self::_activate();
	}

	/**
	 * Flushes the rewrite rules to reflect the permalink changes automatically (if any)
	 *
	 * @global type $wp_rewrite
	 */
	public static function _flush_rewrite_rules()
	{
		global $wp_rewrite;
		// First, we "add" the custom post type via the above written function.

		do_action('cmtoc_flush_rewrite_rules');

		// Clear the permalinks
		flush_rewrite_rules();

		//Call flush_rules() as a method of the $wp_rewrite object
		$wp_rewrite->flush_rules();
	}

	/**
	 * Scoped i18n function
	 * @param type $message
	 * @return type
	 */
	public static function __($message)
	{
		return __($message, CMTOC_SLUG_NAME);
	}

	/**
	 * Scoped i18n function
	 * @param type $message
	 * @return type
	 */
	public static function _e($message)
	{
		return _e($message, CMTOC_SLUG_NAME);
	}

}