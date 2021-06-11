import './styles/home.css';

const scrollDiv = document.querySelector(".scroll-down");
const tuto = document.querySelector("#tuto");

scrollDiv   .addEventListener('click', () => {
    tuto.scrollIntoView({ behavior: "smooth" });
    console.log("here");
})
