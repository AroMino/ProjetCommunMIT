window.scrollTo(0,0);

const folder = document.querySelector(".folder");
const nav = document.querySelector("nav");
const boxes = document.querySelectorAll(".box");
const scrollBar = document.querySelector(".scroll-bar");
const thumb = document.querySelector(".thumb");
const welcome = document.querySelector(".welcome");
const arrow = document.querySelector(".box-arrow");

let folderHeightMax = folder.getBoundingClientRect().top;
let welcomeHeightMax = welcome.getBoundingClientRect().top;
let arrowHeightMax = arrow.getBoundingClientRect().top;

window.addEventListener("scroll",(event) => {
    // animation arrow
    if(arrow.getBoundingClientRect().top >= 0){
        const arrowOpacity = arrow.getBoundingClientRect().top/arrowHeightMax;
        arrow.style.opacity =arrowOpacity.toString();
    }
    else if(arrow.getBoundingClientRect().top < -50) arrow.style.opacity = "0";

    // animation welcome
    if(welcome.getBoundingClientRect().top >= 0){
        const welcomeOpacity = welcome.getBoundingClientRect().top/welcomeHeightMax;
        welcome.style.opacity = welcomeOpacity.toString();
    }
    else if(welcome.getBoundingClientRect().top < -50) welcome.style.opacity = "0";

    // Grossissement de folder lors du scroll
    if(folder.getBoundingClientRect().top >= 0){
        const folderWidth = 100 - folder.getBoundingClientRect().top * 30 / folderHeightMax;
        folder.style.width = folderWidth.toString()+"%";

        if(folder.clientWidth >= window.innerWidth*85/100){
            folder.querySelector(".top").style.backgroundColor = "rgb(25, 3, 6)";
            folder.querySelector(".body-block").style.backgroundColor = "rgb(25, 3, 6)";
        }
        else{
            folder.querySelector(".top").style.backgroundColor = "white";
            folder.querySelector(".body-block").style.backgroundColor = "white";
        }
    }
    else if (folder.getBoundingClientRect().top < 0)folder.style.width = "100%";

    // Apparition du scrollBar et du nav
    if(folder.clientWidth >= window.innerWidth){
        nav.style.backgroundColor = "rgb(25, 3, 6)";
        folder.querySelector(".options").style.opacity = "1";
        scrollBar.style.opacity = "1";
        if(Math.abs(folder.getBoundingClientRect().top < 0)){
            let thumbTop = (Math.abs(folder.getBoundingClientRect().top)/(folder.clientHeight-window.innerHeight))*100;
            thumb.style.top = thumbTop.toString()+"%";
        }
        
    }
    else{
        nav.style.backgroundColor = "transparent";
        folder.querySelector(".options").style.opacity = "0";
        thumb.style.top = "0%";
        scrollBar.style.opacity = "0";
    }

    // Animation des boxes
    boxes.forEach(box => {
        if(box.getBoundingClientRect().bottom < 200) box.style.opacity = "0";
        else box.style.opacity = "1";
    });
});
