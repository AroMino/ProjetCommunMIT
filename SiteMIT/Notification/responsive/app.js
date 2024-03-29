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


button.addEventListener("click",function(){
  const value = search.value;
  const file = document.querySelector(".file");
  window.location.href = file.innerHTML+"?search="+value;
});

document.addEventListener("keypress",function(e){
  if(e.key == "Enter"){
    const file = document.querySelector(".file");
    window.location.href = file.innerHTML+"?search="+search.value;
  }
});

// window.location.href = "./presence_pc_table.php";