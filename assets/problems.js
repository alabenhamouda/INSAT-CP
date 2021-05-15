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

function addSelectedTag(tagName) {
    let tag = $("<span></span>").addClass("tag").text(tagName);
    let remove = $("<i class=\"fas fa-trash\"></i>").addClass("removeTag")
    remove.on('click', function (e) {
        let parent = $(this).parent();
        let val = select.selectpicker('val').filter(t => t != parent.text());
        select.selectpicker('val', val);
        parent.remove();
    })
    tag.append(remove);
    $(".selectedTags").append(tag);
}

function removeSelectedTag(tagName) {
    $(".selectedTags .tag").filter(function () {
        return $(this).text().trim() == tagName;
    }).remove();
}

select.on('changed.bs.select', function (e, clickedIdx, isSelected, previousVal) {
    if (clickedIdx != null) {
        let tag = $('ul.dropdown-menu>li').eq(clickedIdx).text().trim();
        if (isSelected)
            addSelectedTag(tag);
        else
            removeSelectedTag(tag);
    }
})
select.on('loaded.bs.select refreshed.bs.select', function () {
    $('.selectedTags').html("");
    for (let tag of select.selectpicker('val')) {
        addSelectedTag(tag);
    }
})
$("tr .tag").on('click', function (e) {
    let name = $(this).text();
    $(".selectpicker option").filter(function () {
        return $(this).text().trim() == name.trim();
    }).prop("selected", true);
    select.selectpicker('refresh');
    $("form").submit();
    e.stopPropagation();
})
$("#clear").on("click", function () {
    console.log('ok');
    $("#select").val('default').selectpicker("refresh");
});