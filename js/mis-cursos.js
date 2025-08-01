$(document).ready(function() {
  //Obtener parametro X
  const valores = window.location.search;
  const urlParams = new URLSearchParams(valores);
  const id = urlParams.get('id');
  //FIN Obtener parametro X
  
  alert("si llega.");
  valida_sesion();

  $('#salir').on('click', function () {   //Cerrar sesion
    //$(location).attr('href','controls/logout.php');
    const token = localStorage.getItem('token');
    fetch('controls/logout.php', {
      method: 'POST',
      headers: {
        'Authorization': token
      }
    })
    .then(() => {
      localStorage.clear();
      window.location.href = 'login.html';
    });
  });

  function valida_sesion() {
    const token = localStorage.getItem('token');
    console.log("TOKEN:", token);
    if (!token) {
      window.location.href = 'login.html';
    } 
    else {
      fetch('controls/valida_token.php', {
        // headers: {
        //   "Authorization": token
        // }
        body: new URLSearchParams({ token }),
      })
      .then(res => res.json())
      .then(data => {
        console.log("Respuesta:", data);
        if (!data.ok) {
          localStorage.clear();
          alert("Tu sesión ha expirado. Por favor, inicia sesión.");
          window.location.href = 'login.html';
        } else {
           alert("entro.");
          console.log("Bienvenido", data.usuario.Nombre);
          // Guardamos el idcliente para poder usarlo luego
          localStorage.setItem('idcliente', data.usuario.IdCliente);
          lista_miscursos(); // <- Llamamos aquí para asegurar que ya tenemos el id
        }
      });
    }
  }

  
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

  function lista_miscursos() {   //1 Carga los datos del curso en el form
    const idcliente = localStorage.getItem('idcliente'); // Recuperar ID del cliente

    const postData = {
      datos: 1,
      idcliente: idcliente
    };
    const url = 'controls/mis-cursos_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        template = '';
        const tasks = JSON.parse(response);
        n = tasks.length;
        if (n == 0) {
          template += `
            <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch flex-column">
              <div class="card card-warning bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                <font color="white" size=5>
                  Tu compra sigue pendiente de pago. 
                  Una vez realizado tu pago espera un lapso de 1 hora para tener acceso a tus cursos.
                  <br><br>
                  En caso de no tener acceso a tus cursos no dudes en ponerte en contacto con nosotros
                  y con gusto te apoyaremos.
                  </font>
                </div>
              </div>
            </div>`;
        }
        else {
          tasks.forEach(task => {
            ruta = task.b;
            n = ruta.length;
            ruta = 'admin/' + ruta.slice(3,n);
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
                      <a href="curso.php?clv=${task.z}" class="btn btn-sm btn-primary">
                        Entrar al curso
                      </a>
                    </div>
                  </div>
                </div>
            </div>`;
          });
        }
        $('#DivCursos').html(template);  
      }
      else {
        alerta(1,"Ocurrio un problema al cargar el curso");
        console.log('ERROR: ' + response);
      }
    }); 
  }

});
