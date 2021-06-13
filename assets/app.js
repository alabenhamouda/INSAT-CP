/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
import $ from 'jquery';
import './styles/app.css';
import './styles/footer.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'bootstrap';
//import $ from 'jquery/dist/jquery';
//import 'bootstrap';
//require('bootstrap');
//$(document).ready(function() {
// $('[data-toggle="popover"]').popover();
//});
// start the Stimulus application
import './bootstrap';