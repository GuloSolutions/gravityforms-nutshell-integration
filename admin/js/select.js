(function($) {
    'use strict';
    var tags = [];

    $(".chosen-select").chosen({
        disable_search_threshold: 10,
        no_results_text: "Oops, nothing found!",
        width: "65%"
    })

    $("#submit").click(function(e){
        e.preventDefault();
        e.stopPropagation();

        $('*[id*=tags]').each(function() {
            let tag =  $(this).find(":selected").val();
            if (typeof tag !== 'undefined') {
                tags.push(tag);
            }
        });

        console.log(tags);

    });

    function settingsSavedmessage() {
        $("#wp_content_likes_notification").
                fadeIn("slow").
                html('Settings Saved <span class="wp_content_likes_notification-dismiss"><a title="dismiss message">X</a></span>').
                delay(500).
                fadeOut("slow");
        }

        settingsSavedmessage();

        document.getElementById('output').innerHTML = location.search;

})(jQuery);
