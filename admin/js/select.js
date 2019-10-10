(function($) {
    'use strict';

    $(".chosen-select").chosen({
        disable_search_threshold: 10,
        no_results_text: "Oops, nothing found!",
        width: "65%"
    })

    $("#submit").click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var all_tags = {};

        $('*[class*=search-choice]').each(function() {
            var id = '';
            var tag = '';
            var string_tag = '';
            var tags = {};

            string_tag = $(this).text();
            id = $(this).closest('div').parent().attr('data-id');

            if (string_tag) {
                tags.id = id;
                tags.tag = tag;
            }

            if (!$.isEmptyObject(tags)) {
                all_tags = tags;
            }
        });

        var data = {
            'action': 'process_nutshell_tags',
            data: all_tags
        }

        jQuery.ajax({
            url: nutshell_tags.ajax_url,
            data: data,
            method: 'POST',
            success: function(data) {
                settingsSavedmessage();
            }
        });
    });

    function settingsSavedmessage() {
        $("#wp_content_likes_notification").
                fadeIn("slow").
                html('Settings Saved <span class="wp_content_likes_notification-dismiss"><a title="dismiss message">X</a></span>').
                delay(1000).
                fadeOut("slow");
        }
})(jQuery);
