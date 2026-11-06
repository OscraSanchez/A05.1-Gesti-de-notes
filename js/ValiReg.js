
    usuarioValido = false;
    nombreValido = false;
    apellidoValido = false;
    emailValido = false;
    conValido = false;
    con2Valido = false;

function validarUsuario() {
    valor = document.getElementById("usuario").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorUsuario").innerHTML = "El usuario es obligatorio";
        return;
    }

    document.getElementById("errorUsuario").innerHTML = "";
    return usuarioValido = true;
}

function validarNombre() {
    valor = document.getElementById("nombre").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorNombre").innerHTML = "El nombre no puede estar vacío";
        return;
    } else if (valor.length > 100) {
        document.getElementById("errorNombre").innerHTML = "El nombre no puede tener más de 100 caracteres";
        return;
    }

    document.getElementById("errorNombre").innerHTML = "";
    return nombreValido = true;
}

function validarApellido() {
    valor = document.getElementById("apellido").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorApellido").innerHTML = "El apellido no puede estar vacío";
        return;
    } else if (valor.length > 100) {
        document.getElementById("errorApellido").innerHTML = "El apellido no puede tener más de 100 caracteres";
        return;
    }

    document.getElementById("errorApellido").innerHTML = "";
    return apellidoValido = true;
}

function validarEmail() {
    valor = document.getElementById("email").value;

    if (valor == null || valor.trim().length == 0) {
        document.getElementById("errorEmail").innerHTML = "El email no puede estar vacío";
        return;
    } else if (valor.length > 150) {
        document.getElementById("errorEmail").innerHTML = "El email no puede tener más de 150 caracteres";
        return;
    } else if (!/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(valor)) {
        document.getElementById("errorEmail").innerHTML = "El email no tiene un formato válido";
        return;
    }

    document.getElementById("errorEmail").innerHTML = "";
    return emailValido = true;
}

function validarCon() {
    valor = document.getElementById("con").value;

    if (valor == null || valor.length == 0) {
        document.getElementById("errorCon").innerHTML = "La contraseña no puede estar vacía";
        return;
    } else if (valor.length < 8) {
        document.getElementById("errorCon").innerHTML = "La contraseña debe tener al menos 8 caracteres";
        return;
    } else if (valor.length > 30) {
        document.getElementById("errorCon").innerHTML = "La contraseña no puede tener más de 30 caracteres";
        return;
    }

    document.getElementById("errorCon").innerHTML = "";
    return conValido = true;
}

function validarCon2() {
    valor = document.getElementById("con2").value;

    if (valor == null || valor.length == 0) {
        document.getElementById("errorCon2").innerHTML = "La confirmación es obligatoria";
        return;
    } else if (valor != document.getElementById("con").value) {
        document.getElementById("errorCon2").innerHTML = "Las contraseñas no coinciden";
        return;
    }

    document.getElementById("errorCon2").innerHTML = "";
    return con2Valido = true;
}

function comprobarFormulario() {
    if (usuarioValido && nombreValido && apellidoValido && emailValido && conValido && con2Valido) {
        document.getElementById("botonRegistrar").disabled = false;
        document.getElementById("botonRegistrar").classList.add('des');
    } else {
        document.getElementById("botonRegistrar").disabled = true;
        document.getElementById("botonRegistrar").classList.remove('des');
    }
}

document.getElementById("usuario").onblur = validarUsuario;
document.getElementById("nombre").onblur = validarNombre;
document.getElementById("apellido").onblur = validarApellido;
document.getElementById("email").onblur = validarEmail;
document.getElementById("con").onblur = validarCon;
document.getElementById("con2").onblur = validarCon2;

document.getElementById("usuario").onfocus = comprobarFormulario;
document.getElementById("nombre").onfocus = comprobarFormulario;
document.getElementById("apellido").onfocus = comprobarFormulario;
document.getElementById("email").onfocus = comprobarFormulario;
document.getElementById("con").onfocus = comprobarFormulario;
document.getElementById("con2").onfocus = comprobarFormulario;