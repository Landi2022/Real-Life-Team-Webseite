document.addEventListener("DOMContentLoaded", init);
function init(){
    
    //Contact - Page
    const realnameInput = document.getElementById("nameInput");
    const emailInput = document.getElementById("emailInput");
    const alterInput = document.getElementById("alterInput");
    const bewerbungTextInput = document.getElementById("bewerbungTextInput");
    const contactSubmitButton = document.getElementById("loginSubmitButton");

    if(contactSubmitButton){
        contactSubmitButton.addEventListener("click", function() {
            submitContact(emailInput.value, realnameInput.value, alterInput.value, bewerbungTextInput.value);
        });
    }
}

async function submitContact(emailInput, realName, alter, bewerbung) {

    //ajax
    let data = { 
        "name": realName,
        "emailInput": emailInput,
        "alter": alter,
        "bewerbungsText": bewerbung
    };
    let response = await fetch("http://localhost:8000/ajax/bewerbung.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data)
    });

    console.log(response);

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
    

    console.log(discordTag);
}