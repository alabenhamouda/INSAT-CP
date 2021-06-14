import './clipboard';
import './highlight';
import 'mathjax/es5/tex-svg';
import './styles/problem.css';
import dompurify from 'dompurify';
import $ from 'jquery';

console.log("degla")

$(".purify").each(function () {
    $(this).html(dompurify.sanitize($(this).html()));
    console.log(dompurify.removed)
});

// MathJax = {
//     tex: {
//         inlineMath: [['$', '$'], ['\\(', '\\)']]
//     },
//     svg: {
//         fontCache: 'global'
//     }
// };
