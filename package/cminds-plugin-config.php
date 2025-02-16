<?php
ob_start();
include plugin_dir_path(__FILE__) . 'views/plugin_compare_table.php';
$plugin_compare_table = ob_get_contents();
ob_end_clean();
$activation_redirect_wizard = isset(get_option('cmtoc_options')['cmtoc_AddWizardMenu']) ? get_option('cmtoc_options')['cmtoc_AddWizardMenu'] : true;
$cminds_plugin_config = array(
	'plugin-is-pro'				 => FALSE,
	'plugin-has-addons'			 => FALSE,
	'plugin-version'			 => '1.2.6',
	'plugin-abbrev'				 => 'cmtoc',
	'plugin-short-slug'			 => 'cmtoc',
	'plugin-parent-short-slug'	 => '',
    'plugin-affiliate'               => '',
    'plugin-redirect-after-install'  => $activation_redirect_wizard ? admin_url( 'admin.php?page=cmtoc_setup_wizard' ) : admin_url('admin.php?page=cmtoc_settings'),
    'plugin-show-guide'              => TRUE,
    'plugin-guide-text'              => '    <div style="display:block">
        <ol>
            <li>Edit the post or page you where you would like to add the TOC</li>
            <li>Look for the panel <strong>CM Table of Contents (Free version)</strong> and check the box "Search for Table Of Contents items on this post/page"</li>
             <li>Add the shortcode [cmtoc_table_of_contents] to the content. This code will turn into the Table of Contents in the Front-End.</li>
            <li>Publish the content</li>
						<li>Check the Free Version documentation for more information <a href="https://creativeminds.helpscoutdocs.com/article/2221-cm-table-of-contents-cmtoc-free-version-guide" target="_blank">https://creativeminds.helpscoutdocs.com/article/2221-cm-table-of-contents-cmtoc-free-version-guide</a></li>
          </ol>
    </div>',
    'plugin-guide-video-height'      => 240,
    'plugin-guide-videos'            => array(
        array( 'title' => 'Installation tutorial', 'video_id' => '159221615' ),
    ),
    'plugin-upgrade-text'           => 'Good Reasons to Upgrade to Pro',
    'plugin-upgrade-text-list'      => array(
        array( 'title' => 'Introduction to the table of contents', 'video_time' => '0:00' ),
        array( 'title' => 'TOC Custom posts settings', 'video_time' => '0:30' ),
        array( 'title' => 'Advanced TOC general settings', 'video_time' => '0:40' ),
        array( 'title' => 'Advanced TOC elements detection settings', 'video_time' => '1:06' ),
        array( 'title' => 'Advanced TOC styling support', 'video_time' => '1:24' ),
        array( 'title' => 'TOC Shortcode support', 'video_time' => '1:52' ),
        array( 'title' => 'Demo of advanced TOC features', 'video_time' => '2:00' ),
        array( 'title' => 'TOC detection and settings per post', 'video_time' => '2:38' ),
    ),
    'plugin-upgrade-video-height'   => 240,
    'plugin-upgrade-videos'         => array(
        array( 'title' => 'Table of Contents Premium Features', 'video_id' => '130259229' ),
    ),
	'plugin-file'				 => CMTOC_PLUGIN_FILE,
	'plugin-dir-path'			 => plugin_dir_path( CMTOC_PLUGIN_FILE ),
	'plugin-dir-url'			 => plugin_dir_url( CMTOC_PLUGIN_FILE ),
	'plugin-basename'			 => plugin_basename( CMTOC_PLUGIN_FILE ),
    'plugin-campign'             => '?utm_source=cmtocfree&utm_campaign=freeupgrade',
	'plugin-icon'				 => '',
	'plugin-name'				 => CMTOC_NAME,
	'plugin-license-name'		 => CMTOC_NAME,
	'plugin-slug'				 => '',
	'plugin-menu-item'			 => CMTOC_SETTINGS_OPTION,
	'plugin-textdomain'			 => CMTOC_SLUG_NAME,
	'plugin-userguide-key'		 => '271-cm-table-of-contents',
	'plugin-store-url'			 => 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-table-of-content-plugin-for-wordpress?utm_source=cmtocree&utm_campaign=freeupgrade&upgrade=1',
	'plugin-support-url'         => 'https://www.cminds.com/contact/',
	'plugin-review-url'			 => 'https://wordpress.org/support/view/plugin-reviews/cm-table-of-content',
	'plugin-changelog-url'		 => 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-table-of-content-plugin-for-wordpress/#changelog',
	'plugin-licensing-aliases'	 => array( ),
	'plugin-compare-table'	 => $plugin_compare_table,
);