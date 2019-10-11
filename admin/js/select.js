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

        var all_tags = [];

        $('*[class*=search-choice]').each(function() {
            var id = '';
            var tag = '';
            var string_tag = '';
            var tags = {};

            string_tag = $(this).text();
            id = $(this).closest('div').parent().attr('data-id');

            if (string_tag) {
                if (tags.id) {
                    tags.tag_text = string_tag;
                } else {
                    tags.id = id;
                    tags.tag_text = string_tag;
                }
            }

            if (!$.isEmptyObject(tags)) {
                all_tags.push(tags);
            }
        });

        var data = {
            'action': 'process_nutshell_tags',
            'ntags': all_tags
        }

        jQuery.ajax({
            url: nutshell_tags.ajax_url,
            data: data,
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res == '1') {
                    settingsSavedmessage();
                }
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
