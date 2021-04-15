const scrollDiv = document.querySelector(".scroll-down");
const links = document.querySelector("#links");

scrollDiv.addEventListener('click', () => {
    links.scrollIntoView({ behavior: "smooth" });
})