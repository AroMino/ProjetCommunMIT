var swiperHorizontal = new Swiper(".swiper-container", {
  direction: "horizontal",
  loop: false,
  navigation: {
    nextEl: ".swiper-next",
    prevEl: ".swiper-prev",
  },
});

const search = document.querySelector("#global-search");
const button = document.querySelector(".box-search button");
const date = document.querySelector(".date");

button.addEventListener("click",function(){
  const file = document.querySelector(".file");
  window.location.href = file.innerHTML+"?search="+search.value+"&date="+date.value;
});

document.addEventListener("keypress",function(e){
  if(e.key == "Enter"){
    const file = document.querySelector(".file");
    const date = document.querySelector(".date");
    window.location.href = file.innerHTML+"?search="+search.value+"&date="+date.value;
  }
});

document.querySelector('.date').setAttribute('inputmode', 'none');
document.querySelector('date').addEventListener('keydown', (e) => {
    e.preventDefault();
    return false;
});

// window.location.href = "./presence_pc_table.php";