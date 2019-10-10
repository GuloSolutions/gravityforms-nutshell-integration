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
            var tags = {};
            var form_tags = [];

            string_tag = $(this).text();
            id = $(this).closest('div').parent().attr('data-id');

            if (string_tag) {
                tags.id = id;
                tags.tag = tag;
            }

            if (!jQuery.isEmptyObject(tags)) {
                all_tags.push(tags);
            }
        });
        settingsSavedmessage();
    });

    function settingsSavedmessage() {
        $("#wp_content_likes_notification").
                fadeIn("slow").
                html('Settings Saved <span class="wp_content_likes_notification-dismiss"><a title="dismiss message">X</a></span>').
                delay(1000).
                fadeOut("slow");
        }
        settingsSavedmessage();
})(jQuery);
