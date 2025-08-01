$(document).ready(function() {
  // Global
  var datos;
  $('#DivEmail').show();
  $('#DivCodigo').hide();
  $('#DivNewPass').hide();

  function alerta(t,msn) {   //Funcion para mandar las alertas
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: false,
    })
    if (t == 1) {   //1 es para ERROR
      Toast.fire({
        icon: 'error',
        title: msn
      })
    }
    else {
      Toast.fire({
        icon: 'success',
        title: msn
      })
    }
  }

  $('#salir').on('click', function () {   //Cerrar sesion
    $(location).attr('href','controls/logout.php');
  });
  
  $('#BtnSolicita').on('click', function () {      //1 Valida el correo y envía el codigo
    //e.preventDefault();
    const postData = {
      datos: 1,
      email: $('#TxtEmail').val()
    };
    const url = 'controls/forgot-password_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        console.log(response);
        if (response == 'OK') {
          alerta(0,"Se envio un código a tu correo electrónico");
          $('#DivEmail').hide();
          $('#DivCodigo').show();
        }
        else
          alerta(1,"E-mail no registrado");
      }
      else
        alerta(1,"Ocurrio un problema.");
    });
  });

  $('#BtnValida').on('click', function () {      //2 Valida el código
    const postData = {
      datos: 2,
      codigo: $('#TxtCodigo').val()
    };
    const url = 'controls/forgot-password_ctrl.php';
    $.post(url, postData, (response) => {
      console.log(response);
      if(!response.error) {
        if (response == 'OK') {
          $('#DivEmail').hide();
          $('#DivCodigo').hide();
          $('#DivNewPass').show();
        }
        else
          alerta(1,"Código no valido");
      }
      else
        alerta(1,"Ocurrio un problema.");
    });
  });

  $('#FrmForgot').submit(e => {      //3 Actualiza la contraseña
    e.preventDefault();
    if ($('#TxtPass1').val() == $('#TxtPass2').val()) {
      const postData = {
        datos: 3,
        pass: $('#TxtPass1').val()
      };
      const url = 'controls/forgot-password_ctrl.php';
      $.post(url, postData, (response) => {
        console.log(response);
        if(!response.error) {
          if (response == 'OK') {
            alerta(0,"Contraseña cambiada correctamente");
            $(location).attr('href','index.php');
          }
          else
            alerta(1,"Ocurrio un problema");
        }
      });
    }
    else
      alerta(1,"Las contraseñas no coinciden");
  });
  
});
