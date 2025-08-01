$(document).ready(function() {
  //Obtener parametro X
  const valores = window.location.search;
  const urlParams = new URLSearchParams(valores);
  const clv = urlParams.get('clv');
  //FIN Obtener parametro X
  
  carga_contenido();

  $('#salir').on('click', function () {   //Cerrar sesion
    $(location).attr('href','controls/logout.php');
  });
  
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
  
  function carga_contenido() {   //1 Carga el contenido del curso
    const postData = {
      datos: 1,
      clave: clv
    };
    const url = 'controls/curso_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        const tasks = JSON.parse(response);
        //lleno la parte lateral
        template = `
          <h3 class="text-primary"><i class="fas fa-paint-brush"></i>${tasks[0]['a']}</h3>
          <p class="text-muted">${tasks[0]['c']}</p>`;
        $('#DivLateral').html(template);
        //Lleno la imagen
        ruta = tasks[0]['b'];
        n = ruta.length;
        ruta = 'admin/' + ruta.slice(3,n);
        template = `
          <div class="col-12 col-sm-12">
            <div class="info-box bg-light">
              <img src="${ruta}" width="100%" height="100%">
            </div>
          </div>`;
        $('#DivImagen').html(template);
        //Lleno el contenido del curso
        template = '';
        tasks.forEach(task => {
          template += `
            <div class="post">
              <div class="user-block">
                <div class="text-success"><h3>${task.d}</h3></div>
              </div>
              <p>
                <a id="${task.z}" class="descargar_file btn btn-sm btn-primary">Descargar archivo</a>
              </p>
            </div>`;
        });
        $('#DivContenido').html(template);
      }
      else {
        alerta(1,"Ocurrio un problema al cargar el curso");
        console.log('ERROR: ' + response);
      }
    }); 
  }

  document.attachEvent = function( evt, q, fn ) {
    document.addEventListener( evt, ( e ) => {
      if (e.target.matches( q ) ) {
        fn.apply( e.target, [e] );
      }
    });
  };

  document.attachEvent('click','.descargar_file', function() {    //2 Descarga el archivo del curso
    console.log(this.id +" siii!");
    const postData = {
      datos: 2,
      id: this.id
    };
    const url = 'controls/curso_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        partes = response.split(',');
        nombre = partes[0];
        nombre = nombre.replace(/[^a-zA-Z0-9 ]/g, '');
        nombre = nombre.replace(/\s/g, '');
        nombre = nombre.slice(0,14);
        //Preparo el archivo para descargarlo
        ruta = partes[1];
        n = ruta.length;
        ruta = 'admin/' + ruta.slice(3,n);
        let t = $.now();
        var a = document.createElement('a');
        var file_name = nombre;
        a.href = ruta + "?t=" + t;
        a.download = file_name;
        a.click();
      }
      else {
        alerta(1,"Ocurrio un problema al descargar el archivo.");
        console.log('ERROR: ' + response);
      }
    });
  });

});
