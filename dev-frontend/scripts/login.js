function validateEmail(email,pw) {
    
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if(re.test(email)===false || pw.length<1){
       alert("Invalid email or password (minimum of 8 characters)"); 
    }
    else{
        window.location.href = "home.html";
    }
}

