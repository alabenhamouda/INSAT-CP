import ClipboardJS from 'clipboard/dist/clipboard.min';
import $ from 'jquery'
import 'bootstrap'

let cli = new ClipboardJS('.tocopy');

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
                // console.log(this);
                $(this).popover("hide");
            }, 1000)
        });
})

cli.on('success', function (e) {
    e.clearSelection();
});
