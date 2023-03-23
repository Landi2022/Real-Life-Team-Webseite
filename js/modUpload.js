document.addEventListener("DOMContentLoaded", init);
function init(){
    
    //Contact - Page
    const emailInput = document.getElementById("emailInput");
    const autorenInput = document.getElementById("autorenInput");
    const modNameInput = document.getElementById("modNameInput");
    const bewerbungTextInput = document.getElementById("bewerbungTextInput");
    const fileInput = document.getElementById("fileupload");
    const contactSubmitButton = document.getElementById("loginSubmitButton");

    if(contactSubmitButton){
        contactSubmitButton.addEventListener("click", function() {
            submitContact(emailInput.value, autorenInput.value, modNameInput.value, bewerbungTextInput.value, fileInput);
        });
    }
}



async function submitContact(emailInput, autorname, modname, bewerbung, fileInput) {

    //ajax
    let formData = new FormData();
    
    formData.append("emailInput", emailInput);
    formData.append("autor", autorname);
    formData.append("modname", modname);
    formData.append("bewerbungsText", bewerbung);
    formData.append("file", fileInput.files[0]);

    let response = await fetch("http://localhost:8000/ajax/modUpload.php", {
        method: "POST",
        headers: {
            "Accept": "application/json"
        },
        body: formData
    });
    
    console.log(await response);

    let res = await response.json();
    console.log(res);

    //zeige fehler an
    if(response.status == 502) {

    }
    
    if(response.status == 200) {
        
    }

    if(response.status == 501) {
        
    }
    
    if(response.status == 500) {
        
    }
}