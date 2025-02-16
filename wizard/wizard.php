<?php
use com\cminds\tableofcontents\settings\CMTOC_Settings;

class CMTOCF_SetupWizard {

    /* 1. Rename this class
     * 2. Update the "Welcome.." step in /view/wizard.php file
     * 3. Update wizard steps in the setSteps()
     * 4. In the CSS and JS files you can add any custom code for the specific plugin if needed
     * 5. Update the add_submenu_page() to add wizard page correctly, and saveOptions() to update options correctly
     * 6. You can add custom functions to this class if needed
     * 7. Include this file with include_once or require_once
     */

    public static $steps;
    public static $wizard_url;
    public static $wizard_path;
    public static $options_slug = 'cmtoc_'; //change for your plugin needs
    public static $wizard_screen = 'cm-table-of-contents_page_cmtoc_setup_wizard'; //change for your plugin needs
    public static $setting_page_slug = 'cmtoc_settings'; //change for your plugin needs
    public static $plugin_basename;

    public static function init() {
        self::$wizard_url = plugin_dir_url(__FILE__);
        self::$wizard_path = plugin_dir_path(__FILE__);
        self::$plugin_basename = plugin_basename('cm-table-of-content'); //change for your plugin needs
		add_action('wp_loaded', array(__CLASS__, 'wp_loaded'));
        add_action('admin_menu', array(__CLASS__, 'add_submenu_page'),30);
        add_action('wp_ajax_cmtocf_save_wizard_options',[__CLASS__,'saveOptions']);
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueueAdminScripts' ] );
    }
	
	public static function wp_loaded() {
		self::setSteps();
	}
	
    public static function setSteps() {
		
		$listStyleType = CMTOC_Settings::get( 'cmtoc_table_of_contentsListType', 'none' );
		
		$listStyleTypeClass = '';
		if ( 'decimal-indent' === $listStyleType ) {
			$listStyleTypeClass = 'cmtoc_table';
		}
		
		$step1_content = "";
		$step1_content .= "<div class='form-group cmtoc_preview_box_1'>
			<label class='label'>Preview</label>
			<div class='preview_main_div'>
				<div class='preview_div' style='background-color:".CMTOC_Settings::get('cmtoc_table_of_contentsBackgroundColor', '#fff').";border-width:".CMTOC_Settings::get('cmtoc_table_of_contentsBorderWidth', '0px')."; border-style:solid; border-color:".CMTOC_Settings::get('cmtoc_table_of_contentsBorderColor', '#373737').";'>
					<div class='heading'>".CMTOC_Settings::get('cmtoc_table_of_contentsHeaderDescription', 'Table Of Contents')."</div>
					<ul class='".$listStyleTypeClass."' style='list-style-type:".CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').";'>
						<li>Chapter 1
							<ul style='list-style-type:".CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').";'>
								<li>Chapter 1.1</li>
								<li>Chapter 1.2</li>
							</ul>
						</li>
						<li>Chapter 2</li>
						<li>Chapter 3</li>
					</ul>
				</div>
			</div>
		</div>";
		
		$step2_content = '';
		$step2_content .= '<p>You can customize the font size and color for each TOC title level. Levels represent the hierarchy of headings, starting with the largest as Level 0, followed by Level 1, and so on. The plugin supports up to 6 levels.</p>';
		$step2_content .= '<p>In this wizard, you can adjust the first 3 levels. To configure all 6 levels, go to the <a href="'.admin_url( 'admin.php?page='. CMTOCF_SetupWizard::$setting_page_slug ).'">plugin settings</a>.</p>';
		
		$step2_content .= "<form>";
		$step2_content .= wp_nonce_field('wizard-form', '_wpnonce', true, false);
		$step2_content .= "<div class='form-group-row'>";
			$step2_content .= "<h6>Level 0</h6>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title size (in pixels)</label>";
				$step2_content .= "<input type='number' id='cmtoc_table_of_contentsLevel0Size' name='cmtoc_table_of_contentsLevel0Size' value='".str_replace('px', '', CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25'))."'>";
			$step2_content .= "</div>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title color</label>";
				$step2_content .= "<input type='color' id='cmtoc_table_of_contentsLevel0Color' name='cmtoc_table_of_contentsLevel0Color' value='".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Color', '#373737')."'>";
			$step2_content .= "</div>";
		$step2_content .= "</div>";
		
		$step2_content .= "<div class='form-group-row'>";
			$step2_content .= "<h6>Level 1</h6>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title size (in pixels)</label>";
				$step2_content .= "<input type='number' id='cmtoc_table_of_contentsLevel1Size' name='cmtoc_table_of_contentsLevel1Size' value='".str_replace('px', '', CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22'))."'>";
			$step2_content .= "</div>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title color</label>";
				$step2_content .= "<input type='color' id='cmtoc_table_of_contentsLevel1Color' name='cmtoc_table_of_contentsLevel1Color' value='".CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Color', '#373737')."'>";
			$step2_content .= "</div>";
		$step2_content .= "</div>";
		
		$step2_content .= "<div class='form-group-row'>";
			$step2_content .= "<h6>Level 2</h6>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title size (in pixels)</label>";
				$step2_content .= "<input type='number' id='cmtoc_table_of_contentsLevel2Size' name='cmtoc_table_of_contentsLevel2Size' value='".str_replace('px', '', CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19'))."'>";
			$step2_content .= "</div>";
			$step2_content .= "<div class='form-group-col'>";
				$step2_content .= "<label class='label'>Title color</label>";
				$step2_content .= "<input type='color' id='cmtoc_table_of_contentsLevel2Color' name='cmtoc_table_of_contentsLevel2Color' value='".CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Color', '#373737')."'>";
			$step2_content .= "</div>";
		$step2_content .= "</div>";
		
		$step2_content .= "</form>";
		
		$step2_content .= "<div class='form-group cmtoc_preview_box_2'>
			<label class='label'>Preview</label>
			<div class='preview_main_div'>
				<div class='preview_div' style='background-color:".CMTOC_Settings::get('cmtoc_table_of_contentsBackgroundColor', '#fff').";border-width:".CMTOC_Settings::get('cmtoc_table_of_contentsBorderWidth', '0px')."; border-style:solid; border-color:".CMTOC_Settings::get('cmtoc_table_of_contentsBorderColor', '#373737').";'>
					<div class='heading'>".CMTOC_Settings::get('cmtoc_table_of_contentsHeaderDescription', 'Table Of Contents')."</div>
					<ul class='".$listStyleTypeClass."' style='list-style-type:".CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').";'>
						<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Color', '#373737')."'>Level 1
							<ul style='list-style-type:".CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').";'>
								<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Color', '#373737')."'>Level 2</li>
								<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Size', '22px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel1Color', '#373737')."'>Level 2
									<ul style='list-style-type:".CMTOC_Settings::get('cmtoc_table_of_contentsListType', 'none').";'>
										<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Color', '#373737')."'>Level 3</li>
										<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Size', '19px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel2Color', '#373737')."'>Level 3</li>
									</ul>
								</li>
							</ul>
						</li>
						<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Color', '#373737')."'>Level 1</li>
						<li style='font-size:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Size', '25px')."; color:".CMTOC_Settings::get('cmtoc_table_of_contentsLevel0Color', '#373737')."'>Level 1</li>
					</ul>
				</div>
			</div>
		</div>";
		
		$step2_content .= '<style>
		ul.cmtoc_table ul, ul.cmtoc_table { list-style-type:none; counter-reset:item; margin:0; padding:0; }
		ul.cmtoc_table ul > li, ul.cmtoc_table > li { display:table; counter-increment:item; margin-bottom:0.3em; }
		ul.cmtoc_table ul > li:before, ul.cmtoc_table > li:before { content:counters(item, ".") ". "; }
		ul.cmtoc_table li ul > li:before { content:counters(item, ".") " "; }
		</style>';
		
		$step3_content = "";
		$step3_content .= "<div class='form-group cmtoc_preview_box_3'>
			<label class='label'>Preview</label>
			<div class='preview_main_div'>
				<div class='preview_div' style='background-color:".CMTOC_Settings::get('cmtoc_back_to_top_background', '#fff')."; border-width:".CMTOC_Settings::get('cmtoc_back_to_top_border_width', '1px')."; border-style:solid; border-color:".CMTOC_Settings::get('cmtoc_back_to_top_border_color', '#cecece')."; fill:".CMTOC_Settings::get('cmtoc_back_to_top_arrow_color', '#111')."; width:".CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px')."; height:".CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px').";'><svg id='cmtoc_svg-arrow' style='width:".CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px')."; height:".CMTOC_Settings::get('cmtoc_back_to_top_arrow_size', '40px')."; enable-background:new 0 0 64 64;' version='1.1' viewBox='0 0 64 64' xml:space='preserve' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><g id='Icon-Chevron-Left' transform='translate(237.000000, 335.000000)'><polyline class='st0' id='Fill-35' points='-191.3,-296.9 -193.3,-294.9 -205,-306.6 -216.7,-294.9 -218.7,-296.9 -205,-310.6 -191.3,-296.9'/></g></svg></div>
			</div>
		</div>";
		
		$step4_content = "";
		$step4_content .= "<p>The initial setup is complete!</p>";
		$step4_content .= "<p>You can enable the table of contents for specific posts and pages:</p>";
		$step4_content .= "<p>1. Open the post or page you want to edit.<br>2. Scroll down to the <strong>CM Table of Contents</strong> metabox.</p>";
		$step4_content .= "<p>Here, you can:</p>";
		$step4_content .= "<p>1. Enable the first option to display the table of contents at the top of the page automatically.<br>2. If you prefer to display the table of contents in a custom location, enable the second option as well, and add the shortcode <strong>[cmtoc_table_of_contents]</strong> at the desired position in the content.</p>";
		$step4_content .= "<div class='cm_wizard_image_holder'>
								<a href='". self::$wizard_url . "assets/img/wizard_step_4_1.png' target='_blank'>
									<img src='". self::$wizard_url . "assets/img/wizard_step_4_1.png' width='750px' style='border:1px solid #444;' />
								</a>
							  </div>";
			
        self::$steps = [
            1 => [
				'title' => 'TOC Appearance',
				'options' => [
                    0 => [
                        'name' => 'cmtoc_table_of_contentsHeaderDescription',
                        'type' => 'string',
						'title' => 'Table of contents title',
						'hint' => 'Set the label of the Table of Contents title.',
						'value' => 'Table Of Contents',
                    ],
					1 => [
                        'name' => 'cmtoc_table_of_contentsListType',
                        'type' => 'select',
						'title' => 'List type',
						'hint' => 'Set the style of the list elements of the Table of Contents.',
						'value' => 'none',
						'options' => [
                            0 => [
                                'title' => 'None',
                                'value' => 'none'
                            ],
							1 => [
                                'title' => 'Circle',
                                'value' => 'circle'
                            ],
							2 => [
                                'title' => 'Square',
                                'value' => 'square'
                            ],
							3 => [
                                'title' => 'Numbers',
                                'value' => 'decimal'
                            ],
							4 => [
                                'title' => 'Nested numbers',
                                'value' => 'decimal-indent'
                            ],
							5 => [
                                'title' => 'Upper Roman',
                                'value' => 'upper-roman'
                            ],
							6 => [
                                'title' => 'Lower Roman',
                                'value' => 'lower-roman'
                            ],
							7 => [
                                'title' => 'Upper Alphabet',
                                'value' => 'upper-alpha'
                            ],
							8 => [
                                'title' => 'Lower Alphabet',
                                'value' => 'lower-alpha'
                            ]
                        ],
                    ],
					2 => [
                        'name' => 'cmtoc_table_of_contentsBackgroundColor',
                        'type' => 'color',
						'title' => 'Background color',
						'hint' => 'Set the background color of the Table of Contents.',
						'value' => '#fff',
						'preview' => '1',
                    ],
					3 => [
                        'name' => 'cmtoc_table_of_contentsBorderWidth',
                        'type' => 'int',
                        'min' => '0',
						'title' => 'Border width (in pixels)',
						'hint' => 'Set the border width for the Table of Contents section.',
						'value' => '0',
                    ],
					4 => [
                        'name' => 'cmtoc_table_of_contentsBorderColor',
                        'type' => 'color',
						'title' => 'Border color',
						'hint' => 'Set the border color for the Table of Contents section.',
						'value' => '#373737',
						'preview' => '1',
                    ],
                ],
				'content' => $step1_content,
            ],
            2 => [
				'title' =>'TOC Style',
				'content' => $step2_content,
            ],
            3 => ['title' =>'Back to the Top Button',
                'options' => [
                    0 => [
                        'name' => 'cmtoc_jump_back_to_top',
						'type' => 'bool',                
						'title' => 'Show back to the top button',
                        'hint' => 'Enable this option if you want to show the back to the top button.',
						'value' => '0',
                    ],
					1 => [
                        'name' => 'cmtoc_back_to_top_background',
						'type' => 'color',                
						'title' => 'Background color',
                        'hint' => 'Choose the background color for the back to the top button.',
						'value' => '#fff',
						'preview' => '3',
                    ],
					2 => [
                        'name' => 'cmtoc_back_to_top_border_width',
						'type' => 'int',                
						'min' => '0',                
						'title' => 'Border width (in pixels)',
                        'hint' => 'Choose the border width for the back to the top button.',
						'value' => '1',
                    ],
					3 => [
                        'name' => 'cmtoc_back_to_top_border_color',
						'type' => 'color',                
						'title' => 'Border color',
                        'hint' => 'Choose the color for the back to the top button.',
						'value' => '#cecece',
						'preview' => '3',
                    ],
					4 => [
                        'name' => 'cmtoc_back_to_top_arrow_size',
						'type' => 'int',                
						'min' => '0',                
						'title' => 'Arrow size (in pixels)',
                        'hint' => 'Choose the size of the arrow inside the back to the top button.',
						'value' => '40',
                    ],
					5 => [
                        'name' => 'cmtoc_back_to_top_arrow_color',
						'type' => 'color',                
						'title' => 'Arrow color',
                        'hint' => 'Choose the color of the arrow inside the back to the top button.',
						'value' => '#111',
						'preview' => '3',
                    ],
                ],
				'content' => $step3_content,
            ],
            4 => ['title' =>'Enabling TOC',
                'content' => $step4_content,
			],
        ];
        return;
    }

    public static function add_submenu_page() {
        if(CMTOC_Settings::get('cmtoc_AddWizardMenu', 1)){
            add_submenu_page( 'cmtoc_settings', 'Setup Wizard', 'Setup Wizard', 'manage_options', self::$options_slug . 'setup_wizard', [__CLASS__,'renderWizard'], 20 );
        }
    }

    public static function enqueueAdminScripts() {
        $screen = get_current_screen();		
        if ($screen && $screen->id === self::$wizard_screen) {
            wp_enqueue_style('wizard-css', self::$wizard_url . 'assets/wizard.css');
            wp_enqueue_script('wizard-js', self::$wizard_url . 'assets/wizard.js', array('jquery'));
            wp_localize_script('wizard-js', 'wizard_data', ['ajaxurl' => admin_url('admin-ajax.php')]);
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
        }
    }
	
    public static function saveOptions() {
        if (isset($_POST['data'])) {
            // Parse the serialized data
            parse_str($_POST['data'], $formData);
            if(!wp_verify_nonce($formData['_wpnonce'],'wizard-form')){
                wp_send_json_error();
            }
            foreach($formData as $key => $value){
                if( !str_contains($key, self::$options_slug) ){
                    continue;
                }
                if(is_array($value)){
                    $sanitized_value = array_map('sanitize_text_field', $value);
					CMTOC_Settings::set($key, $sanitized_value);
                    continue;
                }
                $sanitized_value = sanitize_text_field($value);
				if($key == 'cmtoc_table_of_contentsBorderWidth' || $key == 'cmtoc_table_of_contentsLevel0Size' || $key == 'cmtoc_table_of_contentsLevel1Size' || $key == 'cmtoc_table_of_contentsLevel2Size' || $key == 'cmtoc_back_to_top_border_width' || $key == 'cmtoc_back_to_top_arrow_size') {
					CMTOC_Settings::set($key, $sanitized_value.'px');
				} else {
					CMTOC_Settings::set($key, $sanitized_value);	
				}			
            }
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public static function renderWizard() {
        require 'view/wizard.php';
    }

    public static function renderSteps() {
        $output = '';
        $steps = self::$steps;
        foreach($steps as $num => $step) {
            $output .= "<div class='cm-wizard-step step-{$num}' style='display:none;'>";
            $output .= "<h1>" . self::getStepTitle($num) . "</h1>";
            $output .= "<div class='step-container'>
                            <div class='cm-wizard-menu-container'>" . self::renderWizardMenu($num)." </div>";
            $output .= "<div class='cm-wizard-content-container'>";
			if(isset($step['options'])) {
                $output .= "<form>";
                $output .= wp_nonce_field('wizard-form');
                foreach($step['options'] as $option){
                    $output .=  self::renderOption($option);
                }
                $output .= "</form>";
            }
			if (isset($step['content'])) {
                $output .= $step['content'];
            }
            $output .= '</div></div>';
            $output .= self::renderStepsNavigation($num);
            $output .= '</div>';
        }
        return $output;
    }

    public static function renderStepsNavigation($num) {
        $settings_url = admin_url( 'admin.php?page='. self::$setting_page_slug );
        $output = "<div class='step-navigation-container'><button class='prev-step' data-step='{$num}'>Previous</button>";
        if($num == count(self::$steps)){
            $output .= "<button class='finish' onclick='window.location.href = \"$settings_url\" '>Finish</button>";
        } else {
			$output .= "<button class='next-step' data-step='{$num}'>Next</button>";
        }
        $output .= "<p><a href='$settings_url'>Skip the setup wizard</a></p></div>";
        return $output;
    }

    public static function renderOption($option) {
        switch($option['type']) {
            case 'bool':
                return self::renderBool($option);
            case 'int':
                return self::renderInt($option);
            case 'string':
                return self::renderString($option);
            case 'radio':
                return self::renderRadioSelect($option);
            case 'select':
                return self::renderSelect($option);
            case 'color':
                return self::renderColor($option);
            case 'multicheckbox':
                return self::renderMulticheckbox($option);
        }
    }
	
	public static function renderBool($option) {
		$val = CMTOC_Settings::get($option['name'], $option['value']);
        $checked = checked(1, CMTOC_Settings::get($option['name'], $option['value']), false);
        $output = "<div class='form-group'>";
		$output .= "<label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
		$output .= "<input type='hidden' name='{$option['name']}' value='0'>";
        $output .= "<input type='checkbox' id='{$option['name']}' name='{$option['name']}' class='toggle-input' value='{$val}' {$checked}>";
		$output .= "<label for='{$option['name']}' class='toggle-switch'></label>";
		$output .= "</div>";
        return $output;
    }

    public static function renderInt($option) {
        $min = isset($option['min']) ? "min='{$option['min']}'" : '';
        $max = isset($option['max']) ? "max='{$option['max']}'" : '';
        $step = isset($option['step']) ? "step='{$option['step']}'" : '';
        $value = CMTOC_Settings::get( $option['name'], $option['value']);
		
		$value = str_replace('px', '', $value);
        
		return "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <input type='number' id='{$option['name']}' name='{$option['name']}' value='{$value}' {$min} {$max} {$step}/>
            </div>";
    }

    public static function renderString($option) {
        $value = CMTOC_Settings::get( $option['name'], $option['value'] );
        return "<div class='form-group'>
                <label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>
                <input type='text' id='{$option['name']}' name='{$option['name']}' value='{$value}'/>
            </div>";
    }

    public static function renderRadioSelect($option) {
        $options = $option['options'];
        $output = "<div class='form-group'>";
		if($option['hint'] != '') {
			$output .= "<label class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
		} else {
			$output .= "<label class='label'>{$option['title']}</label>";
		}
        $output .= "<div>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
		foreach($options as $item) {
            $checked = checked($item['value'], CMTOC_Settings::get($option['name'], $option['value']), false);
            $output .= "<input type='radio' id='{$option['name']}_{$item['value']}' name='{$option['name']}' value='{$item['value']}' {$checked}/>
                <label for='{$option['name']}_{$item['value']}'>{$item['title']}</label><br>";
        }
        $output .= "</div>";
		$output .= "</div>";
        return $output;
    }

    public static function renderColor($option) {
        ob_start();
		?>
        <script>jQuery(function ($) {
			$('input[name="<?php echo esc_attr($option['name']); ?>"]').wpColorPicker({
				change: function (event, ui) {
					var element = event.target;
					var color = ui.color.toString();
					var control_name = "<?php echo $option['name']; ?>";
					var preview_id = "<?php echo $option['preview']; ?>";
					if(preview_id) {
						if(preview_id == '1') {
							if(control_name == "cmtoc_table_of_contentsBackgroundColor") {
								$('.cmtoc_preview_box_1').find('.preview_div').css('background-color', color);
								$('.cmtoc_preview_box_2').find('.preview_div').css('background-color', color);
							} else if(control_name == "cmtoc_table_of_contentsBorderColor") {
								$('.cmtoc_preview_box_1').find('.preview_div').css('border-color', color);
								$('.cmtoc_preview_box_2').find('.preview_div').css('border-color', color);
							}
						}
						if(preview_id == '3') {
							if(control_name == "cmtoc_back_to_top_background") {
								$('.cmtoc_preview_box_3').find('.preview_div').css('background-color', color);
							} else if(control_name == "cmtoc_back_to_top_border_color") {
								$('.cmtoc_preview_box_3').find('.preview_div').css('border-color', color);
							} else if(control_name == "cmtoc_back_to_top_arrow_color") {
								$('.cmtoc_preview_box_3').find('.preview_div').css('fill', color);
							}
						}
					}
				}
			});
		});</script>
		<?php
        $output = ob_get_clean();
        $value = CMTOC_Settings::get( $option['name'], $option['value']);
        $output .= "<div class='form-group'><label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
        $output .= sprintf('<input type="text" name="%s" value="%s" />', esc_attr($option['name']), esc_attr($value));
        $output .= "</div>";
        return $output;
    }

    public static function renderSelect($option) {
        $options = $option['options'];
		$output = "<div class='form-group'>";
        $output .= "<label for='{$option['name']}' class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
		$output .= "<select id='{$option['name']}' name='{$option['name']}'>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
        foreach($options as $item) {
			$selected = selected($item['value'],CMTOC_Settings::get( $option['name'] ),false);
			$output .= "<option value='{$item['value']}' {$selected}>{$item['title']}</option>";
		}
		$output .= "</select></div>";
		return $output;
	}
	
    public static function renderMulticheckbox($option) {
        $options = $option['options'];
        $output = "<div class='form-group'>";
        $output .= "<label class='label'>{$option['title']}<div class='cm_field_help' data-title='{$option['hint']}'></div></label>";
		$output .= "<div>";
        if(is_callable($option['options'], false, $callable_name)) {
            $options = call_user_func($option['options']);
        }
		foreach($options as $item) {
            $checked = in_array($item['value'], CMTOC_Settings::get( $option['name'], $option['value'] )) ? 'checked' : '';
            $output .= "<input type='checkbox' id='{$option['name']}_{$item['value']}' name='{$option['name']}[]' value='{$item['value']}' {$checked}/>
                <label for='{$option['name']}_{$item['value']}'>{$item['title']}</label><br>";
        }
        $output .= "</div>";
        $output .= "</div>";
        return $output;
    }

    public static function renderWizardMenu($current_step) {
        $steps = self::$steps;
        $output = "<ul class='cm-wizard-menu'>";
        foreach ($steps as $key => $step) {
            $num = $key;
            $selected = $num == $current_step ? 'class="selected"' : '';
            $output .= "<li {$selected} data-step='$num'>Step $num: {$step['title']}</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    public static function getStepTitle($current_step) {
        $steps = self::$steps;
        $title = "Step {$current_step}: ";
        $title .= $steps[$current_step]['title'];
        return $title;
    }

    //Custom functions
	
}

CMTOCF_SetupWizard::init();