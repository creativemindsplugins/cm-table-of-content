<?php

class CMTOC_Metabox
{
	protected static $filePath = '';
	protected static $cssPath = '';
	protected static $jsPath = '';
	public static $lastQueryDetails = array();
	public static $calledClassName;

	const DISPLAY_NOWHERE = 0;
	const DISPLAY_EVERYWHERE = 1;
	const DISPLAY_ONLY_ON_PAGES = 2;
	const DISPLAY_EXCEPT_ON_PAGES = 3;

	public static function init()
	{
		if( empty(self::$calledClassName) )
		{
			self::$calledClassName = __CLASS__;
		}

		add_action('add_meta_boxes', array(self::$calledClassName, 'cmtoc_RegisterBoxes'));
		add_action('save_post', array(self::$calledClassName, 'cmtoc_save_postdata'));
		add_action('update_post', array(self::$calledClassName, 'cmtoc_save_postdata'));
		add_filter('cmtoc_add_properties_metabox', array(self::$calledClassName, 'metaboxProperties'));
	}


	/**
	 * Registers the metaboxes
	 */
	public static function cmtoc_RegisterBoxes()
	{

		$defaultPostTypes = get_option( 'cmtoc_disable_metabox_all_post_types' ) ? get_post_types() : array( 'table-of-content', 'post', 'page' );
		$disableBoxPostTypes = apply_filters( 'cmtoc_disable_metabox_posttypes', $defaultPostTypes );
		foreach( $disableBoxPostTypes as $postType )
		{
			add_meta_box( 'table-of-content-exclude-box', 'CM Table of Contents (Free version)', array( self::$calledClassName, 'cmtoc_render_my_meta_box' ), $postType, 'normal', 'high' );
		}

		do_action('cmtoc_register_boxes');
	}


	public static function metaboxProperties( $properties )
	{
		$properties['table_of_content_disable_for_page'] = CMTOC_Pro::__('Search for Table Of Contents items on this post/page.');
		$properties[ 'table_of_content_auto_shortcode_disable' ] = CMTOC_Pro::__( 'Disable automatic display of TOC at top of the content (Without adding the <code>[cmtoc_table_of_contents]</code> shortcode)?' );
		return $properties;
	}


	public static function cmtoc_table_of_contents_meta_box_fields()
	{
		$metaBoxFields = apply_filters('cmtoc_add_properties_metabox', array());
		return $metaBoxFields;
	}


	public static function cmtoc_render_my_meta_box($post)
	{
		$result = array();

		foreach(self::cmtoc_table_of_contents_meta_box_fields() as $key => $fieldValueArr)
		{
			$optionContent = '<p><label for="' . $key . '" class="blocklabel">';
			$fieldValue = get_post_meta($post->ID, '_' . $key, true);

			if( $fieldValue === '' && !empty($fieldValueArr['default']) )
			{
				$fieldValue = $fieldValueArr['default'];
			}

			if( is_string($fieldValueArr) )
			{
				$label = $fieldValueArr;
				$optionContent .= '<input type="checkbox" name="' . $key . '" id="' . $key . '" value="1" ' . checked('1', $fieldValue, false) . '>';
			}
			elseif( is_array($fieldValueArr) )
			{
				$label = isset($fieldValueArr['label']) ? $fieldValueArr['label'] : CMTOC_Pro::__('No label');

				if( array_key_exists('options', $fieldValueArr) )
				{
					$options = isset($fieldValueArr['options']) ? $fieldValueArr['options'] : array('' => CMTOC_Pro::__('-no options-'));
					$optionContent .= '<select name="' . $key . '" id="' . $key . '">';
					foreach($options as $optionKey => $optionLabel)
					{
						$optionContent .= '<option value="' . $optionKey . '" ' . selected($optionKey, $fieldValue, false) . '>' . $optionLabel . '</option>';
					}
					$optionContent .= '</select>';
				}
				else if( array_key_exists('callback', $fieldValueArr) )
				{
					$optionContent .= call_user_func($fieldValueArr['callback'], $key, $fieldValueArr, $post);
				}
				else
				{
					$type = isset($fieldValueArr['type']) ? $fieldValueArr['type'] : 'text';
					$htmlAtts = isset($fieldValueArr['html_atts']) ? $fieldValueArr['html_atts'] : '';
					$optionContent .= '<input type="' . $type . '" name="' . $key . '" id="' . $key . '" value="' . $fieldValue . '" ' . $htmlAtts . '>';
				}
			}

			if( !empty($label) )
			{
				$optionContent .= '&nbsp;&nbsp;&nbsp;' . $label . '</label>';
			}

			$optionContent .= '</p>';

			$result[] = $optionContent;
		}

		$result = apply_filters('cmtoc_edit_properties_metabox_array', $result);

		echo implode('', $result);
	}

	public static function cmtoc_save_postdata($post_id)
	{
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$postType = isset($post['post_type']) ? $post['post_type'] : '';

		do_action('cmtoc_on_table_of_content_item_save_before', $post_id, $post);

		$disableBoxPostTypes = apply_filters('cmtoc_disable_metabox_posttypes', array('table-of-content', 'post', 'page'));
		if( in_array($postType, $disableBoxPostTypes) )
		{
			/*
			 * Disables the parsing of the given page
			 */
			$disableParsingForPage = 0;
			if( isset($post["table_of_content_disable_for_page"]) && $post["table_of_content_disable_for_page"] == 1 )
			{
				$disableParsingForPage = 1;
			}
			update_post_meta($post_id, '_table_of_content_disable_for_page', $disableParsingForPage);

			

			/*
			 * Disables the showing of table-of-content on given page
			 */
			$disableTableOfContentForPage = 0;
			if( isset($post["table_of_content_disable_table_of_content_for_page"]) && $post["table_of_content_disable_table_of_content_for_page"] == 1 )
			{
				$disableTableOfContentForPage = 1;
			}
			update_post_meta($post_id, '_table_of_content_disable_table_of_content_for_page', $disableTableOfContentForPage);

			/*
			 * Part for "table-of-content" items only starts here
			 */
			foreach(array_keys(self::cmtoc_table_of_contents_meta_box_fields()) as $value)
			{
				$metaValue = (isset($post[$value])) ? $post[$value] : 0;
				if( is_array($metaValue) )
				{
					delete_post_meta($post_id, '_' . $value);
					$metaValue = array_filter($metaValue);
				}
				update_post_meta($post_id, '_' . $value, $metaValue);
			}
		}
	}
}