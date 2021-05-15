import $ from 'jquery';
import './styles/problems.css';
import 'bootstrap'
import '../node_modules/bootstrap-select/dist/js/bootstrap-select'
import '../node_modules/bootstrap-select/dist/css/bootstrap-select.css'
import '../node_modules/popper.js'

$(document).ready(function () {
    $('#table tr').click(function () {
        var href = $(this).find("a").attr("href");
        if (href) {
            window.location = href;
        }
    });
});
document.addEventListener('click', (e) => {
    if (e.target.className == 'label navbar bg-dark') {
        e.target.parentNode.classList.toggle('active');
        //.classList.toggle('active');
    }
});
let select = $(".selectpicker");
$("tr .tag").on('click', function (e) {
    let name = $(this).text();
    $(".selectpicker option").filter(function () {
        return $(this).text().trim() == name.trim();
    }).prop("selected", true);
    select.selectpicker('refresh');
    $("form").submit();
    e.stopPropagation();
})