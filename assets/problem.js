import CodeMirror from 'codemirror/lib/codemirror'
import 'codemirror/mode/clike/clike'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/duotone-dark.css'
import 'codemirror/theme/monokai.css'
import 'codemirror/theme/ayu-mirage.css'
import 'codemirror/addon/hint/show-hint'
import 'codemirror/addon/hint/show-hint.css'
import 'codemirror/addon/edit/closebrackets'
import 'codemirror/addon/edit/matchbrackets'
import './styles/problem.css'
import ClipboardJS from 'clipboard/dist/clipboard.min';
import $ from 'jquery'
import 'bootstrap'
import 'mathjax/es5/tex-svg'

let cli = new ClipboardJS('.tocopy');
//TODO add a copied message
$(function () {
    $(".tocopy")
        .popover({
            content: "Text copied!",
            placement: "bottom",
            trigger: "manual"
        })
        .on("click", function () {
            $(this).popover("show");
            setTimeout(() => {
                console.log(this);
                $(this).popover("hide");
            }, 1000)
        })
})
cli.on('success', function (e) {
    // console.info('Action:', e.action);
    // console.info('Text:', e.text);
    // console.info('Trigger:', e.trigger);

    e.clearSelection();
});
if (typeof CodeMirror !== 'undefined') {
    console.log(CodeMirror.mimeModes)
    $('.editor').each(function () {
        var editor = CodeMirror.fromTextArea(this, {
            mode: CodeMirror.mimeModes['text/x-c++src'],
            theme: "ayu-mirage",
            lineNumbers: true,
            extraKeys: {
                "Ctrl-Space":
                    "autocomplete"
            },
            matchBrackets: true,
            autoCloseBrackets: true,
            styleActiveLine: true
        });
        $(this).data('editor', editor);
    });
}
// MathJax = {
//     tex: {
//         inlineMath: [['$', '$'], ['\\(', '\\)']]
//     },
//     svg: {
//         fontCache: 'global'
//     }
// };
$('#btn-open-file').click(function () {
    $('#input-open-file').trigger('click');
});
$('#input-open-file').on('change', function (e) {
    var fileData = e.target.files[0];
    var reader = new FileReader();
    reader.onload = function () {
        $('.editor').data('editor').doc.setValue(reader.result);
    };
    reader.readAsText(fileData);
});
