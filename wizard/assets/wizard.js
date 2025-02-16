jQuery(document).ready(function ($) {
    const steps = $('.cm-wizard-step');

    // Next step button click event
    $('.next-step').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.eq(currentStep).hide();
        steps.eq(currentStep + 1).show();
    });

    // Previous step button click event
    $('.prev-step').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.eq(currentStep).hide();
        steps.eq(currentStep - 1).show();
    });

    // Step link click event
    $('.cm-wizard-menu li').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.hide();
        steps.eq(currentStep).show();
    });

    $('.finish').on('click', function (e) {
        if ($('form').length == 0) {
            return;
        }
        let formData = $('form').serialize();
        $.ajax({
            url: wizard_data.ajaxurl , // WordPress AJAX URL
            type: 'POST' ,
            data: {
                action: 'cmtocf_save_wizard_options' , // Action name for the AJAX handler
                data: formData
            } ,
            success: function (response) {
            } ,
            error: function () {
                alert("An error occurred while saving options.");
            }
        });
    });

    $('.next-step').on('click' , function () {
        // Serialize form data
        let form = $(this).closest('.cm-wizard-step').find('form');

        if (form.length == 0) {
            return;
        }

        let formData = form.serialize();

        // AJAX call to save options
        $.ajax({
            url: wizard_data.ajaxurl , // WordPress AJAX URL
            type: 'POST' ,
            data: {
                action: 'cmtocf_save_wizard_options' , // Action name for the AJAX handler
                data: formData
            } ,
            success: function (response) {
            } ,
            error: function () {
                alert("An error occurred while saving options.");
            }
        });
    });

    var $body = $('body');

    $body.on('mouseenter' , '.cm_field_help' , function () {
        if ($(this).find('.cm_field_help--wrap').length > 0) {
            return;
        }
        var helpHtml = $(this).attr('data-title');
        var $helpItemWrapHeight = "style='min-height:" + $(this).parent().outerHeight() + "px'";
        var $helpItemWrap = $("<div class='cm_field_help--wrap'" + $helpItemWrapHeight + "><div class='cm_field_help--text'></div></div>");

        $(this).append($helpItemWrap);

        var $helpItemText = $(this).find('.cm_field_help--text');
        $helpItemText.html(helpHtml);
        $helpItemText.html($helpItemText.text());

        setTimeout(function () {
            $helpItemWrap.addClass('cm_field_help--active');
        } , 300);
    }).on('mouseleave' , '.cm_field_help' , function () {
        var $helpItem = $(this).find('.cm_field_help--wrap');
        setTimeout(function () {
            $helpItem.removeClass('cm_field_help--active');
        } , 600);
        setTimeout(function () {
            $helpItem.remove();
        } , 800);

    });

	//custom code below
	
	$body.on('click' , '.cm-wizard-step .toggle-input' , function () {
		var that = $(this);
		if(that.prop('checked') == true) {
			that.val(1);
		} else {
			that.val(0);
		}
	});
	
	// step 1
	$('#cmtoc_table_of_contentsHeaderDescription').on('keyup', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_1').find('.preview_div .heading').html(that.val());
		$('.cmtoc_preview_box_2').find('.preview_div .heading').html(that.val());
	});
	
	$('#cmtoc_table_of_contentsListType').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_1').find('.preview_div ul').css('list-style-type', that.val());
		$('.cmtoc_preview_box_2').find('.preview_div ul').css('list-style-type', that.val());
		if(that.val() == 'decimal-indent') {
			$('.cmtoc_preview_box_1').find('.preview_div > ul').addClass('cmtoc_table');
			$('.cmtoc_preview_box_2').find('.preview_div > ul').addClass('cmtoc_table');
		} else {
			$('.cmtoc_preview_box_1').find('.preview_div > ul').removeClass('cmtoc_table');
			$('.cmtoc_preview_box_2').find('.preview_div > ul').removeClass('cmtoc_table');
		}
	});
	
	$('#cmtoc_table_of_contentsBorderWidth').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_1').find('.preview_div').css('border-width', that.val()+'px');
		$('.cmtoc_preview_box_2').find('.preview_div').css('border-width', that.val()+'px');
	});
	
	// step 2
	$('#cmtoc_table_of_contentsLevel0Size').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_2').find('.preview_div > ul > li').css('font-size', that.val()+'px');
	});
	$('#cmtoc_table_of_contentsLevel0Color').wpColorPicker({
		change: function (event, ui) {
			var color = ui.color.toString();
			$('.cmtoc_preview_box_2').find('.preview_div > ul > li').css('color', color);
		}
	});
	$('#cmtoc_table_of_contentsLevel1Size').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_2').find('.preview_div > ul > li > ul > li').css('font-size', that.val()+'px');
	});
	$('#cmtoc_table_of_contentsLevel1Color').wpColorPicker({
		change: function (event, ui) {
			var color = ui.color.toString();
			$('.cmtoc_preview_box_2').find('.preview_div > ul > li > ul > li').css('color', color);
		}
	});
	$('#cmtoc_table_of_contentsLevel2Size').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_2').find('.preview_div > ul > li > ul > li > ul > li').css('font-size', that.val()+'px');
	});
	$('#cmtoc_table_of_contentsLevel2Color').wpColorPicker({
		change: function (event, ui) {
			var color = ui.color.toString();
			$('.cmtoc_preview_box_2').find('.preview_div > ul > li > ul > li > ul > li').css('color', color);
		}
	});
	
	// step 3
	$('#cmtoc_back_to_top_border_width').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_3').find('.preview_div').css('border-width', that.val()+'px');
	});
	
	$('#cmtoc_back_to_top_arrow_size').on('change', function (e) {
		var that = $(this);
		$('.cmtoc_preview_box_3').find('.preview_div').css('width', that.val()+'px');
		$('.cmtoc_preview_box_3').find('.preview_div').css('height', that.val()+'px');
		$('.cmtoc_preview_box_3').find('.preview_div #cmtoc_svg-arrow').css('width', that.val()+'px');
		$('.cmtoc_preview_box_3').find('.preview_div #cmtoc_svg-arrow').css('height', that.val()+'px');
	});
	
});