$(document).ready(function() {
  // Global
  var datos;
  $('#PanelAlerta').hide();

  $('#salir').on('click', function () {   //Cerrar sesion
    $(location).attr('href','controls/logout.php');
  });
  
  $('#FrmLogin').submit(e => {      //1 Valida los datos de sesión
    e.preventDefault();
    const postData = {
      datos: 1,
      email: $('#TxtEmail').val(),
      pass: $('#TxtPass').val()
    };
    const url = 'controls/login_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        if (response == 'OK') {
          $(location).attr('href','index.php');
        }
        else {
          $('#PanelAlerta').show();

        }

      }
    });
  });
  
});
