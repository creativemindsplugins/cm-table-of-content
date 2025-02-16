jQuery(function($) {
	
    var jump_back_btn = window.cmtoc_data.jump_back_btn;
    if ( jump_back_btn === "1" && $('.cmtoc_table_of_contents_wrapper').length ) {
		
        var button = '<div class="cmtoc_to_top_btn" style="display:none;" >';
        button += '<svg id="cmtoc_svg-arrow" style="enable-background:new 0 0 64 64;" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Icon-Chevron-Left" transform="translate(237.000000, 335.000000)"><polyline class="st0" id="Fill-35" points="-191.3,-296.9 -193.3,-294.9 -205,-306.6 -216.7,-294.9 -218.7,-296.9 -205,-310.6 -191.3,-296.9"/></g></svg>';
        button += '</div>';
        $( 'body' ).append( button );

        function jumpBackBtnOnScroll() {
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                $('.cmtoc_to_top_btn').show()
            } else {
                $( '.cmtoc_to_top_btn' ).hide();
            }
        }

        $('.cmtoc_to_top_btn').click( function(){
            $( 'html, body' ).animate( { scrollTop: 0 } );
        });
		
    }
	
    window.onscroll = function() {
        if ( jump_back_btn === "1" && $('.cmtoc_table_of_contents_wrapper').length ) {
            jumpBackBtnOnScroll();
        }
    };
	
});