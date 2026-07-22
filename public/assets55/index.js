//index.js

let menuicn = document.querySelector(".menuicn");
let nav = document.querySelector(".navcontainer1");

menuicn.addEventListener("click", () => {
    nav.classList.toggle("navclose");
})