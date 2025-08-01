$(document).ready(function() {
  // Global
  var datos;
  $('#PanelAlerta').hide();

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
  
  $('#FrmPass').submit(e => {      //1 Valida los datos de sesión
    e.preventDefault();
    if ($('#TxtPass1').val() == $('#TxtPass2').val()) {
      const postData = {
        datos: 1,
        pass: $('#TxtPass1').val()
      };
      const url = 'controls/password_ctrl.php';
      $.post(url, postData, (response) => {
        console.log(response);
        if(!response.error) {
          if (response == 'OK')
            alerta(0,"Contraseña cambiada correctamente")
          else
            alerta(1,"Ocurrio un problema");
        }
      });
    }
    else
      alerta(1,"Las contraseñas no coinciden");
  });
  
});
