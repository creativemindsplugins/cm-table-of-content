
(function ($) {

    $(document).ready(function () {

        /*
         * Added in 2.4.9 (shows/hides the explanations to the synonyms/abbreviations)
         */
        $(document).on('click showHideInit', '.cm-showhide-handle', function () {
            var $this = $(this), $parent, $content;

            $parent = $this.parent();
            $content = $this.siblings('.cm-showhide-content');

            if (!$parent.hasClass('closed'))
            {
                $content.hide();
                $parent.addClass('closed');
            }
            else
            {
                $content.show();
                $parent.removeClass('closed');
            }
        });

        $('.cm-showhide-handle').trigger('showHideInit');

        /*
         * CUSTOM REPLACEMENTS - END
         */

        if ($.fn.tabs) {
            $('#cmtoc_tabs').tabs({
                activate: function (event, ui) {
                    window.location.hash = ui.newPanel.attr('id').replace(/-/g, '_');
                },
                create: function (event, ui) {
                    var tab = location.hash.replace(/\_/g, '-');
                    var tabContainer = $(ui.panel.context).find('a[href="' + tab + '"]');
                    if (typeof tabContainer !== 'undefined' && tabContainer.length)
                    {
                        var index = tabContainer.parent().index();
                        $(ui.panel.context).tabs('option', 'active', index);
                    }
                }
            });
        }

        $('.cmtoc_field_help_container').each(function () {
            var newElement,
                    element = $(this);

            newElement = $('<div class="cmtoc_field_help"></div>');
            newElement.attr('title', element.html());

            if (element.siblings('th').length)
            {
            element.siblings('th').append(newElement);
            }
            else
            {
                element.siblings('*').append(newElement);
            }
            element.remove();
        });

        $('.cmtoc_field_help').tooltip({
            show: {
                effect: "slideDown",
                delay: 100
            },
            position: {
                my: "left top",
                at: "right top"
            },
            content: function () {
                var element = $(this);
                return element.attr('title');
            },
            close: function (event, ui) {
                ui.tooltip.hover(
                        function () {
                            $(this).stop(true).fadeTo(400, 1);
                        },
                        function () {
                            $(this).fadeOut("400", function () {
                                $(this).remove();
                            });
                        });
            }
        });

    });

})(jQuery);