function validaUser(){
    value = document.getElementById('user').value;
    err = document.getElementById('errU')
    error= ""

    if(value == "" || value === null){
        error = "El campo no puede esta vacio";
    }

    if(!(err.value = "") && error == ""){
        err.textContent="";
        err.classList.remove("errores");
        return;
    }
    if(!error == ""){
        err.textContent=error;
        err.className ="errores";
        return;
    }
}

function validaCon(){
    value = document.getElementById('con').value;
    err = document.getElementById('errC')
    error= ""

    if(value == "" || value === null){
        error = "El campo no puede esta vacio";
    }

    if(!(err.value = "") && error == ""){
        err.textContent="";
        err.classList.remove("errores");
        return;
    }
    if(!error == ""){
        err.textContent=error;
        err.className ="errores";
        return;
    }
}



document.getElementById('user').onblur = validaUser;
document.getElementById('con').onblur = validaCon;