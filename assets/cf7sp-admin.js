jQuery(document).ready(function ($) {

    console.log(11);

    var $tabs = $("#post-type-tabs");
    $tabs.tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
    $("li", $tabs).removeClass("ui-corner-top").addClass("ui-corner-left");
});