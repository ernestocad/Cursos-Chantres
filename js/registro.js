$(document).ready(function() {
  // Global
  var datos;
  const formulario = document.getElementById('formCliente');

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

  llenaCmbPais();

  function orden_venta() {   //Funcion para mandar las alertas
    datos = "5";          //5 Genero órden de compra
    url = "controls/registro_ctrl.php?datos=" + datos;
    $(location).attr('href',url);
  }

  function llenaCmbPais(){      //1 Llena CmbPais
    datos = "1";
    $.ajax({
      url: 'controls/registro_ctrl.php',
      data: {datos},
      type: 'POST',
      error: function (xhr, ajaxOptions, thrownError, request, error, textStatus) {
        alert(textStatus + ' - ' + xhr.status +  ' - ' + ajaxOptions.status +  ' - ' + thrownError +  ' - ' + request +  ' - ' + error);
      },
      success: function (response) {
        if(!response.error) {
          const tasks = JSON.parse(response);
          template = '<option value="0">-- Seleccione tu pais</option>';
          tasks.forEach(task => {
            template += `<option value="${task.z}">${task.a}</option>`
          });
          $('#CmbPais').html(template);
        }
        else
          console.log('Error de: ' + response);
      }
    })
  }

  $('#CmbPais').on('change', function () {   //2 Llena CmbEstado
    var idpais = $('#CmbPais').val();
    datos = "2," + idpais;
    $.ajax({
      url: 'controls/registro_ctrl.php',
      data: {datos},
      type: 'POST',
      error: function (xhr, ajaxOptions, thrownError, request, error, textStatus) {
        alert(textStatus + ' - ' + xhr.status +  ' - ' + ajaxOptions.status +  ' - ' + thrownError +  ' - ' + request +  ' - ' + error);
      },
      success: function (response) {
        if(!response.error) {
          const tasks = JSON.parse(response);
          template = '';
          tasks.forEach(task => {
            template += `<option value="${task.z}">${task.a}</option>`
          });
          template += `<option value="-1">OTRO</option>`
          $('#CmbEstado').html(template);
        }
      }
    })
  });

  $('#TxtEmail').on('change', function () {   //3 Valido el correo que no exista
    email = $('#TxtEmail').val();
    datos = "3," + email;
    $.ajax({
      url: 'controls/registro_ctrl.php',
      data: {datos},
      type: 'POST',
      error: function (xhr, ajaxOptions, thrownError, request, error, textStatus) {
        alert(textStatus + ' - ' + xhr.status +  ' - ' + ajaxOptions.status +  ' - ' + thrownError +  ' - ' + request +  ' - ' + error);
      },
      success: function (response) {
        if(!response.error) {
          if (response == 1) {
            $('#TxtEmail').val('');
            alerta(1,"El correo ya existe. Inicie sesión  con su cuenta para continuar.");
          }
        }
        else
          console.log(response);
      }
    })
  });

  formulario.addEventListener('submit', function (e) {  //4 Guardo al nuevo cliente
    e.preventDefault(); // Evita recarga
    // Referencia al botón y al icono
    const btnPagar = document.getElementById('btnPagar');
    // Deshabilita el botón
    btnPagar.disabled = true;
    // Cambia el icono a spinner rotando
    btnPagar.innerHTML = '<i class="fa-solid fa-spinner spinner-rotate"></i> Procesando...';

    const formData = new FormData(this);
    formData.append('datos', 4); // Indicador de operación

    //Obtengo el texto del CmbPais
    const paisSelect = document.getElementById('CmbPais'); //por default se manda el value del combo
    var textoVisible = paisSelect.options[paisSelect.selectedIndex].text;
    formData.append('pais_texto', textoVisible);

    //Obtengo el texto del CmbEstado
    const estadoSelect = document.getElementById('CmbEstado'); //por default se manda el value del combo
    textoVisible = estadoSelect.options[estadoSelect.selectedIndex].text;
    formData.append('estado_texto', textoVisible);
    // Agrega el valor de ClvCurso desde localStorage
    formData.append('clvcurso', localStorage.getItem('ClvCurso'));
    
    fetch('controls/registro_ctrl.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.href = data.init_point; // Redirige a Mercado Pago
      } else {
        btnPagar.disabled = false;
        btnPagar.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Pagar';
        alerta(1, data.mensaje || "Ocurrió un error inesperado");
      }
    })
    
  });





  
  // $('#FrmRegistro').submit(e => {      //4 Guardo al nuevo cliente
  //   e.preventDefault();
  //   const postData = {
  //     datos: 4,
  //     nombre: $('#TxtNombre').val(),
  //     apellido: $('#TxtApellido').val(),
  //     telefono: $('#TxtTelefono').val(),
  //     email: $('#TxtEmail').val(),
  //     pais: $('#CmbPais option:selected').text(),
  //     estado: $('#CmbEstado option:selected').text()
  //   };
  //   const url = 'controls/registro_ctrl.php';
  //   $.post(url, postData, (response) => {
  //     console.log('Me respondio ' + response);
  //     if(!response.error) {
  //       if (response == 'OK') {
  //         console.log(response);
  //         orden_venta();
  //       }
  //       else {
  //         alerta(1,"Ocurrio un problema al guardar tus datos personales");
  //         console.log(response);

  //       }

  //     }
  //   });
  // });

  $('#BtnConekta').on('click', function () {   //Cerrar sesion
    alerta(1,"JALO");
  });

  
  
});
