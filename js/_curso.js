$(document).ready(function() {
  function populateStorage() {
    localStorage.setItem("bgcolor", "red");
    localStorage.setItem("font", "Helvetica");
    localStorage.setItem("images", "mbr-1920x1285.jpg");
  
    localStorage.clear();
  }
  //Obtener parametro X
  const valores = window.location.search;
  const urlParams = new URLSearchParams(valores);
  const id = urlParams.get('idx');
  //FIN Obtener parametro X
  audio = 0;
  let ruta = "";
  
  cargadatos();
  
  function cargadatos() {   //1 Carga los datos del curso en el form
    const postData = {
      datos: 1,
      id: id
    };
    const url = 'mis-cursos/controls/_curso_ctrl.php';
    $.post(url, postData, (response) => {
      if(!response.error) {
        let tasks = JSON.parse(response);
        $('#DivNmbCurso').html(tasks.a);
        precio = '<br><h4>Costo del curso ';
        if (tasks.k == 0)
          precio =  precio + ' <b>$' + tasks.d + ' ' + tasks.l + '</b>';
        else
          precio =  precio + ' <del>$' + tasks.k + '</del>' + ' <b>$' + tasks.d + ' ' + tasks.l + '</b>';
        $('#DivAnuncio').html(precio);
        $('#DivAnuncio2').html(precio);
        $('#DivNmbCurso2').html(tasks.a);
        $('#DivDescripcion').html(tasks.c);
        if (tasks.e == 0 && tasks.f ==0)
          $('#DivDEMOS').hide();
        else
          $('#DivDEMOS').show();
        if (tasks.e == 0)
          $('#DivDemoPDF').hide();
        else {
          ruta = tasks.g;
          n = ruta.length;
          ruta = "mis-cursos/admin/" + ruta.slice(3,n);
          $('#DivDemoPDF').show();
        }
        if (tasks.f == 0)
          $('#DivDemoMP3').hide();
        else {
          rutamp3 = tasks.h;
          n = rutamp3.length;
          rutamp3 = "mis-cursos/admin/" + rutamp3.slice(3,n);
          template = `<audio id="myaudio" controls>
              <source src="${rutamp3}"  type="audio/mp3">
            </audio>`;
          $('#DivPlayMP3').html(template);
          $('#DivDemoMP3').show();
        }
        rutaimg = tasks.b;
        n = rutaimg.length;
        rutaimg = "mis-cursos/admin/" + rutaimg.slice(3,n);
        $("#ImgCurso").attr("src",rutaimg);
        $('#ListaTemas').html(tasks.z);
      }
      else 
        console.log('ERROR: ' + response);
    }); 
  }

  $('#BtnVerPDF').click(function(){
    //let t = $.MD5($.now());
    let t = $.now();
    var a = document.createElement('a');
    var file_name = "demo_pdf";
    a.href = ruta + "?t=" + t;
    a.download = file_name;
    a.click();
  })

  $('#BtnCompra1').click(function(){  //2 Obtengo el Id del curso e inicio el registro
    clvcurso = id;
    localStorage.setItem('ClvCurso', clvcurso);
    $(location).attr('href','mis-cursos/registro.html');
    // const postData = {
    //   datos: 2,
    //   clvcurso: id
    // };
    // const url = 'mis-cursos/controls/_curso_ctrl.php';
    // $.post(url, postData, (response) => {
    //   if(!response.error) {
    //     if (response == 'OK') {
    //       $(location).attr('href','mis-cursos/registro.html');
    //     }
    //     else {
    //       alerta(1,'Ocurrio un problema');
    //       console.log('Error en ' + response);
    //     }
    //   }
    // });
  })

  $('#BtnCompra2').click(function(){  //2 Obtengo el Id del curso e inicio el registro
    clvcurso = id;
    localStorage.setItem('ClvCurso', clvcurso);
    $(location).attr('href','mis-cursos/registro.html');  
    // const postData = {
    //   datos: 2,
    //   clvcurso: id
    // };
    // const url = 'mis-cursos/controls/_curso_ctrl.php';
    // $.post(url, postData, (response) => {
    //   if(!response.error) {
    //     if (response == 'OK') {
    //       $(location).attr('href','mis-cursos/registro.html');
    //     }
    //     else {
    //       alerta(1,'Ocurrio un problema');
    //       console.log('Error en ' + response);
    //     }
    //   }
    // });
  })

});
