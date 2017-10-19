jQuery(document).ready(function ($) {

    $("#post-type-tabs")
        .tabs()
        .addClass("ui-tabs-vertical ui-helper-clearfix")
        .find("li")
        .removeClass("ui-corner-top")
        .addClass("ui-corner-left");

});