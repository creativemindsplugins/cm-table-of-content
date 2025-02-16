<div class="wrap">
    <h2>
        <div id="icon-<?php echo CMTOC_MENU_OPTION; ?>" class="icon32">
            <br />
        </div>
        <?php CMTOC_Pro::_e(CMTOC_NAME); ?>
    </h2>
    <?php CMTOC_Pro::cmtoc_showNav(); ?>
    <?php echo $content; ?>
</div>
<div style="clear:both;"></div>
<?php echo do_shortcode('[cminds_free_activation id="cmtoc"]'); ?>
<div class="cminds_settings_description">
    <p>
        <strong>Supported Shortcodes:</strong> <a href="javascript:void(0)" onclick="jQuery( this ).parent().next().slideToggle()">Show/Hide</a>
    </p>
    <ul style="display:none;list-style-type:disc;margin-left:20px;">
        <li><strong>Display Table of Contents</strong> - [cmtoc_table_of_contents]</li>
    </ul>
</div>
<br/>
<?php if (!empty($messages)): ?>
    <div class="updated" style="clear:both; margin-left: 0;"><p><?php echo $messages; ?></p></div>
<?php endif; ?>
<br/>
<?php
use com\cminds\tableofcontents\settings\CMTOC_Settings;
?>
<div class="clear"></div>
<div id="cminds_settings_container">
	<div>
        <form method="post">
            <div style="float:right; margin-bottom: 15px;">
                <input onclick="jQuery('tr:has(.onlyinpro), .onlyinpro').toggleClass('hide'); return false;" type="submit" name="cmtt_toggleProOptions" value="Show/hide Pro options" class="filter-enabled-button-n"/>
            </div>
        </form>
    </div>
    <div class="settings-view-controls">
        <div>
            <button id="one-column" class="view-button dashicons dashicons-menu-alt2"/>
            <button id="responsive" class="view-button dashicons dashicons-editor-code active"/>
        </div>
        <div>
            <button id="show-enabled" class="filter-enabled-button">Show enabled options</button>
            <button id="show-disabled" class="filter-enabled-button">Show disabled options</button>
        </div>
    </div>
    <form method="post" id="cminds_settings_form">
        <div id="cminds_settings_search--container">
            <input id="cminds_settings_search" placeholder="Search in settings..."><span id="cminds_settings_search_clear">&times;</span>
        </div>
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="update" />
        <div id="cm_settings_tabs" class="invitationCodesSettingsTabs">
            <?php
            CMTOC_Settings::renderSettingsTabsControls();
            CMTOC_Settings::renderSettingsTabs();
            ?>
            <p class="submit" style="clear:left">
                <input id="cminds_settings_save" type="submit"  value="<?php echo 'Save Changes' ?>" name="<?php echo CMTOC_Settings::abbrev('_saveSettings'); ?>" />
            </p>
        </div>
    </form>
</div>
<div class="clear"></div>