$(document).ready(function() {
  //Obtener parametro X
  const valores = window.location.search;
  const urlParams = new URLSearchParams(valores);
  const id = urlParams.get('id');
  //FIN Obtener parametro X
  
  lista_cursos();

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

  function lista_cursos() {   //1 Carga los cursos en venta
    const postData = {
      datos: 1
    };
    const url = 'controls/cursos-venta_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        template = '';
        const tasks = JSON.parse(response);
        tasks.forEach(task => {
          ruta = task.b;
          n = ruta.length;
          ruta = 'admin/' + ruta.slice(3,n);
          precio = 'Costo del curso ';
          if (task.e == 0)
            precio =  precio + ' <b>$' + task.d + ' ' + task.f + '</b><br>';
          else
            precio =  precio + ' <del>$' + task.e + '</del>' + ' <b>$' + task.d + ' ' + task.f + '</b><br>';
          template += `
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
              <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>${task.a}</b></h2>
                      <p class="text-muted text-sm">${task.c}</p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="${ruta}" alt="user-avatar" class="img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                    ${precio}
                  </div>
                </div>

                

                
              
                <div class="card-footer bg-primary">
                  <div class="text-right">
                    Pagar con Tarjetas o en Oxxo
                    <a id="${task.z}" class="comprar btn btn-sm btn-info">
                      Comprar
                    </a>
                  </div>
                </div>
                
                <div class="card-footer bg-warning">
                  <div class="text-right">
                    Pagar con Paypal
                    <a id="${task.z}" class="comprar-paypal btn btn-sm btn-info">
                      Comprar
                    </a>
                  </div>
                </div>

              </div>
            </div>`
        });
        $('#DivCursos').html(template);  
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

  document.attachEvent('click','.comprar', function() {    //2 Genera la orden de venta del curso
    idcurso = this.id;
    datos = "2," + idcurso;
    url = "controls/cursos-venta_ctrl.php?datos=" + datos;
    $(location).attr('href',url);
  });

  document.attachEvent('click','.comprar-paypal', function() {    //2 Genera la orden de venta del curso
    console.log('paypal');
    idcurso = this.id;
    datos = "3," + idcurso;
    url = "controls/cursos-venta_ctrl.php?datos=" + datos;
    //$(location).attr('href',url);
  });

});
