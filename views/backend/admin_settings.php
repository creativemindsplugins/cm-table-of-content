<?php if( !empty($messages) ): ?>
    <div class="updated"="clear:both"><p><?php echo $messages; ?></p></div>
<?php endif; ?>

<br/>

<br/>
<?php  echo do_shortcode( '[cminds_free_activation id="cmtoc"]' ); ?>

<div style="height:15px;"></div>

<div class="cminds_settings_description">

    <?php
// check permalink settings
    if( get_option('permalink_structure') == '' )
    {
        echo '<span style="color:red">Your WordPress Permalinks needs to be set to allow plugin to work correctly. Please Go to <a href="' . admin_url() . 'options-permalink.php" target="new">Settings->Permalinks</a> to set Permalinks to Post Name.</span><br><br>';
    }
    ?>

</div>

<?php
// include plugin_dir_path(__FILE__) . '/call_to_action.phtml';
?>

<br/>
<div class="clear"></div>

<form method="post">
    <?php wp_nonce_field('update-options'); ?>
    <input type="hidden" name="action" value="update" />


    <div id="cmtoc_tabs" class="table-of-contentSettingsTabs">
        <div class="table_of_content_loading"></div>

        <?php
        CMTOC_Pro::renderSettingsTabsControls();

        CMTOC_Pro::renderSettingsTabs();
        ?>

        <div id="tabs-55">
            <div class='block'>

              <table class="form-table"><tbody>
                        <tr>
                            <td><?php echo do_shortcode( '[cminds_upgrade_box id="cmtoc"]' ); ?></td>
                        </tr>
                    </tbody></table>

            </div>
        </div>
        <div id="tabs-2">
            <div class="block">
                <h3>Display Settings</h3>
                <table class="floated-form-table form-table">
                    <tr valign="top">
                        <th scope="row">Only show <?php echo CMTOC_NAME ?> on single posts/pages (not Homepage, authors, category etc.)?</th>
                        <td>
                            <input type="hidden" name="cmtoc_table_of_contentsOnlySingle" value="0" />
                            <input type="checkbox" name="cmtoc_table_of_contentsOnlySingle" <?php checked(true, get_option('cmtoc_table_of_contentsOnlySingle')); ?> value="1" />
                        </td>
                        <td colspan="2" class="cmtoc_field_help_container">Select this option if you wish to only highlight <?php echo CMTOC_NAME ?> when viewing a single page/post.
                            This can be used so Tables of Content aren't displayed on your homepage, or author pages and other taxonomy related pages.</td>
                    </tr>
                </table>
                <div class="clear"></div>
            </div>
            <div class="block">
                <h3>Shortcodes</h3>
                <p>Use shortcode <strong>[cmtoc_table_of_contents]</strong> to define the exact location of the TOC within post or page .</p>

                <div class="clear"></div>
            </div>

            <div class="block">
                <h3>Performance &amp; Debug</h3>
                <table class="floated-form-table form-table">
                    <tr valign="top">
                        <th scope="row">Only highlight on "main" WP query?</th>
                        <td>
                            <input type="hidden" name="cmtoc_table_of_contentOnMainQuery" value="0" />
                            <input type="checkbox" name="cmtoc_table_of_contentOnMainQuery" <?php checked(1, get_option('cmtoc_table_of_contentOnMainQuery')); ?> value="1" />
                        </td>
                        <td colspan="2" class="cmtoc_field_help_container">
                            <strong>Warning: Don't change this setting unless you know what you're doing</strong><br/>
                            Select this option if you wish to only highlight table-of-contents on main table-of-content query.
                            Unchecking this box may fix problems with highlighting table-of-contents on some themes which manipulate the WP_Query.</td>
                    </tr>
                </table>
                <div class="clear"></div>
            </div>
        </div>
        <div id="tabs-3">
            <div class="block">
                <h3>Table of Contents - Labels</h3>
                <table class="floated-form-table form-table">
                    <tr valign="top">
                        <th scope="row">Header</th>
                        <td>
                            <input type="text" name="cmtoc_table_of_contentsHeaderDescription" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsHeaderDescription', 'Table Of Contents')); ?>" />
                        </td>
                        <td colspan="2" class="cmtoc_field_help_container">Set the label which appears above the Table of Contentss on the page.</td>
                    </tr>
                </table>
            </div>
            <div class="block">
                <h3>Table of Contents - Element Selector</h3>
                <table class="floated-form-table form-table">
                    <tr valign="top">
                        <th scope="row">Level 0:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel0Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel0Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel0Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel0Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel0Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel0Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 0-level table of content elements. Defaults to: h1</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Level 1:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel1Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel1Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel1Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel1Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel1Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel1Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 1-level table of content elements. Defaults to: h2</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Level 2:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel2Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel2Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel2Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel2Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel2Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel2Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 2-level table of content elements. Defaults to: h3</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Level 3:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel3Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel3Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel3Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel3Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel3Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel3Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 3-level table of content elements. Defaults to: h4</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Level 4:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel4Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel4Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel4Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel4Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel4Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel4Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 4-level table of content elements. Defaults to: h5</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Level 5:</th>
                        <td>
                            <span class="cmtoc_custom_selctors_label"><span>Tag:</span><input type="text" name="cmtoc_table_of_contentsLevel5Tag" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel5Tag')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Class:</span><input type="text" name="cmtoc_table_of_contentsLevel5Class" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel5Class')); ?>" /></span>
                            <span class="cmtoc_custom_selctors_label"><span>Id:</span><input type="text" name="cmtoc_table_of_contentsLevel5Id" value="<?php echo esc_attr(get_option('cmtoc_table_of_contentsLevel5Id')); ?>" /></span>
                        <td colspan="2" class="cmtoc_field_help_container">Set the selector attributes for the 5-level table of content elements. Defaults to: h6</td>
                    </tr>
                </table>
            </div>

            <?php
            $additionalFootnoteTabContent = apply_filters('cmtoc_settings_table_of_content_tab_content_after', '');
            echo $additionalFootnoteTabContent;
            ?>
        </div>
        <!-- Start Server information Module -->
        <div id="tabs-1">
            <div class='block'>

                 <table class="form-table"><tbody>
                        <tr>
                            <td><?php echo do_shortcode( '[cminds_free_guide id="cmtoc"]' ); ?></td>
                        </tr>
                    </tbody></table>

            </div>
        </div>
    </div>
        <p class="submit"="clear:left">
            <input type="submit" class="button-primary" value="<?php CMTOC_Pro::_e('Save Changes') ?>" name="cmtoc_table_of_contentSave" />
        </p>
</form>
