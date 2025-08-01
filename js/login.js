$(document).ready(function() {
  $('#PanelAlerta').hide();

  $('#salir').on('click', function () {
    // Borrar localStorage y redirigir
    localStorage.clear();
    window.location.href = 'login.html';  // o tu pantalla de login
  });

  $('#FrmLogin').submit(e => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('email', $('#TxtEmail').val());
    formData.append('pass', $('#TxtPass').val());

    fetch('controls/login_ctrl.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.ok) {
        localStorage.setItem('token', data.token);
        localStorage.setItem('usuario', JSON.stringify({
          id: data.idcliente,
          nombre: data.nombre
        }));
        window.location.href = 'index.php';
      } else {
        $('#PanelAlerta').show();   // alert(data.msg);
      }
    });
  });

});