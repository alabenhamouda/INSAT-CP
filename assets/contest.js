import $ from 'jquery';
$(document).ready(function() {
    $('#table tr').click(function() {
        var href = $(this).find("a").attr("href");
        if(href) {
            window.location = href;
        }
    });
});