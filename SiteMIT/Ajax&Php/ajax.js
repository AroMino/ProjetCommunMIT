function read(){
    let xhr = null;
    if(window.XMLHttpRequest) xhr = new XMLHttpRequest();
    else if(window.ActiveXObject) xhr = new ActiveXObject("Microsoft.XMLHTTP");

    xhr = new XMLHttpRequest();
    xhr.responseType = "json";

    xhr.open("GET","./base.php",true);

    xhr.onreadystatechange = function(){
        if(xhr.readyState == 4 && xhr.status == 200){
            const result = xhr.response;
            console.log(result);
        }
    };
    xhr.send();
}