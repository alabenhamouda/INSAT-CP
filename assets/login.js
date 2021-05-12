/*import $ from 'jquery/dist/jquery.min';
$(document).ready(function(){
	AOS.init();
	$('[data-bss-hover-animate]')
		.mouseenter( function(){ var elem = $(this); elem.addClass('animated ' + elem.attr('data-bss-hover-animate')) })
		.mouseleave( function(){ var elem = $(this); elem.removeClass('animated ' + elem.attr('data-bss-hover-animate')) });
});*/
import './styles/login.css';

const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

window.onload = () => {
    window.history.replaceState(container.className, 'first');
}

window.onpopstate = e => {
    console.log(e.state);
    container.className = e.state;
}

sign_up_btn.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
    window.history.pushState(container.className, "signup", "/signup");
});

sign_in_btn.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
    window.history.pushState(container.className, "login", "/login");
});