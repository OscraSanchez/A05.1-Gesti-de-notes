function validarUsuario() {
    valor = document.getElementById("usuario").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorUsuario").innerHTML = "El usuario es obligatorio";
        return false;
    }

    document.getElementById("errorUsuario").innerHTML = "";
    return true;
}

function validarNombre() {
    valor = document.getElementById("nombre").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorNombre").innerHTML = "El nombre no puede estar vacío";
        return false;
    } else if (valor.length > 100) {
        document.getElementById("errorNombre").innerHTML = "El nombre no puede tener más de 100 caracteres";
        return false;
    }

    document.getElementById("errorNombre").innerHTML = "";
    return true;
}

function validarApellido() {
    valor = document.getElementById("apellido").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorApellido").innerHTML = "El apellido no puede estar vacío";
        return false;
    } else if (valor.length > 100) {
        document.getElementById("errorApellido").innerHTML = "El apellido no puede tener más de 100 caracteres";
        return false;
    }

    document.getElementById("errorApellido").innerHTML = "";
    return true;
}

function validarEmail() {
    valor = document.getElementById("email").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorEmail").innerHTML = "El email no puede estar vacío";
        return false;
    } else if (valor.length > 150) {
        document.getElementById("errorEmail").innerHTML = "El email no puede tener más de 150 caracteres";
        return false;
    } else if (!/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(valor)) {
        document.getElementById("errorEmail").innerHTML = "El email no tiene un formato válido";
        return false;
    }

    document.getElementById("errorEmail").innerHTML = "";
    return true;
}

function validarCon() {
    valor = document.getElementById("con").value;

    if (valor == null || valor.length == 0) {
        document.getElementById("errorCon").innerHTML = "La contraseña no puede estar vacía";
        return false;
    } else if (valor.length < 8) {
        document.getElementById("errorCon").innerHTML = "La contraseña debe tener al menos 8 caracteres";
        return false;
    } else if (valor.length > 30) {
        document.getElementById("errorCon").innerHTML = "La contraseña no puede tener más de 30 caracteres";
        return false;
    }

    document.getElementById("errorCon").innerHTML = "";
    return true;
}

function validarCon2() {
    valor = document.getElementById("con2").value;

    if (valor == null || valor.length == 0) {
        document.getElementById("errorCon2").innerHTML = "La confirmación es obligatoria";
        return false;
    } else if (valor != document.getElementById("con").value) {
        document.getElementById("errorCon2").innerHTML = "Las contraseñas no coinciden";
        return false;
    }

    document.getElementById("errorCon2").innerHTML = "";
    return true;
}

function comprobarFormulario() {
    usuarioValido = validarUsuario();
    nombreValido = validarNombre();
    apellidoValido = validarApellido();
    emailValido = validarEmail();
    conValido = validarCon();
    con2Valido = validarCon2();

    if (usuarioValido && nombreValido && apellidoValido && emailValido && conValido && con2Valido) {
        document.getElementById("botonRegistrar").disabled = false;
    } else {
        document.getElementById("botonRegistrar").disabled = true;
    }
}

document.getElementById("usuario").onblur = comprobarFormulario;
document.getElementById("usuario").onkeyup = comprobarFormulario;
document.getElementById("nombre").onblur = comprobarFormulario;
document.getElementById("nombre").onkeyup = comprobarFormulario;
document.getElementById("apellido").onblur = comprobarFormulario;
document.getElementById("apellido").onkeyup = comprobarFormulario;
document.getElementById("email").onblur = comprobarFormulario;
document.getElementById("email").onkeyup = comprobarFormulario;
document.getElementById("con").onblur = comprobarFormulario;
document.getElementById("con").onkeyup = comprobarFormulario;
document.getElementById("con2").onblur = comprobarFormulario;
document.getElementById("con2").onkeyup = comprobarFormulario;