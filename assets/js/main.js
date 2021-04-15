const scrollDiv = document.querySelector(".scroll-down");
const links = document.querySelector("#links");
const cards = document.querySelectorAll(".card");

scrollDiv.addEventListener('click', () => {
    links.scrollIntoView({ behavior: "smooth" });
})

window.addEventListener('resize', adjustCardsHeight);
window.addEventListener('load', adjustCardsHeight);

function adjustCardsHeight() {
    cards.forEach(el => {
        el.style.height = "";
    })

    let maxHeight = 0;

    cards.forEach(el => {
        let h = window.getComputedStyle(el).height;
        let heightNumber = h.slice(0, -2) - 0;
        maxHeight = Math.max(maxHeight, heightNumber);
    })

    cards.forEach(el => {
        el.style.height = maxHeight + "px";
    })
}