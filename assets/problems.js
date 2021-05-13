import $ from 'jquery';
import './styles/problems.css';
$(document).ready(function() {
    $('#table tr').click(function() {
        var href = $(this).find("a").attr("href");
        if(href) {
            window.location = href;
        }
    });
});