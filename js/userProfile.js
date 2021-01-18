document.getElementById("image-cropper").onclick = toggleUserOptions;

function toggleUserOptions(){ 
    if (this.classList.contains("clicked")){
        this.classList.remove("clicked");  
        document.getElementById("logout-button").classList.remove("shown");
        if (document.getElementById("account-button") !== null) {document.getElementById("account-button").classList.remove("shown");}
        if (document.getElementById("home-button") !== null) {document.getElementById("home-button").classList.remove("shown");}
    } else {
        this.classList.add("clicked"); 
        document.getElementById("logout-button").classList.add("shown");
        if (document.getElementById("account-button") !== null) {document.getElementById("account-button").classList.add("shown");}
        if (document.getElementById("home-button") !== null) { console.log("no"); document.getElementById("home-button").classList.add("shown");}
    }
}