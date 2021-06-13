import $ from 'jquery';
import './styles/contests.css';
import 'bootstrap/js/dist/util';
import 'bootstrap/js/dist/dropdown';
const form=document.getElementById('form');
$(document).ready(function() {
    $('#table tr').click(function() {
        var href = $(this).find("a").attr("href");
        if(href) {
            window.location = href;
        }
    });
});
document.addEventListener('click',(e)=>{
    if(e.target.className=='label navbar bg-dark'){
        e.target.parentNode.classList.toggle('active');
        //.classList.toggle('active');
    }
});