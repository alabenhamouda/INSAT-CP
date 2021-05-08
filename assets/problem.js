import './styles/problem.css'
import ClipboardJS from 'clipboard/dist/clipboard.min';
import $ from 'jquery'
import 'bootstrap'
let cli=new ClipboardJS('.tocopy');
//TODO add a copied message
$(function(){
    $(".tocopy")
        .popover({
            content: "Text copied!",
            placement: "bottom",
            trigger: "manual"
        })
        .on("click", function() {
            $(this).popover("show");
            setTimeout(() => {
                console.log(this);
                $(this).popover("hide");
            }, 1000)
        })
})
cli.on('success', function(e) {
    // console.info('Action:', e.action);
    // console.info('Text:', e.text);
    // console.info('Trigger:', e.trigger);

    e.clearSelection();
});

console.log("here");