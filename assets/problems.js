import $ from 'jquery';
import './styles/problems.css';
import 'bootstrap'
import '../node_modules/bootstrap-select/dist/js/bootstrap-select'
import '../node_modules/bootstrap-select/dist/css/bootstrap-select.css'
import '../node_modules/popper.js'
$(document).ready(function() {
    $('#table tr').click(function() {
        var href = $(this).find("a").attr("href");
        if(href) {
            window.location = href;
        }
    });
});