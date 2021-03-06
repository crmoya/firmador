function renderPDF(dataURI){
  var BASE64_MARKER = ';base64,';

  var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
  var base64 = dataURI.substring(base64Index);

  //arr = chunkinize(base64, 1000);

  var pdfData = window.atob(base64);

  // Loaded via <script> tag, create shortcut to access PDF.js exports.
  var pdfjsLib = window['pdfjs-dist/build/pdf'];

  // The workerSrc property shall be specified.
  pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

  // Using DocumentInitParameters object to load binary data.
  var loadingTask = pdfjsLib.getDocument({data: pdfData});
  loadingTask.promise.then(function(pdf) {

    $('#previewer').empty();
    $('#previewer').hide();
    
    
    // Fetch the first page
    var pageNumber = 1;
    
    while(pageNumber <= pdf.numPages){
      pdf.getPage(pageNumber).then(function(page) {
        var scale = 1.5;
        var viewport = page.getViewport({scale: scale});
  
        // Prepare canvas using PDF page dimensions
        var canvas = document.createElement("canvas");
        canvas.id = "canvas-page-" + pageNumber;
        $('#previewer').append(canvas);
        var context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        var ancho = $(window).width() - canvas.width - 50;
        $('#previewer').css('width', (canvas.width + 50)+ "px");
        $('#previewer').css('left', ancho/2 + 'px');
        $('#previewer').show();
        $('#firmar').css('display', 'inline-block');
        // Render PDF page into canvas context
        var renderContext = {
          canvasContext: context,
          viewport: viewport
        };
        var renderTask = page.render(renderContext);
        renderTask.promise.then(function () {
          $("#espereDocumento").hide();
        });
      });
      pageNumber++;
    }
    
    
  }, function (reason) {
    // PDF loading error
    console.error(reason);
  });
}

function callWS(documentos){
  const url='http://localhost:8080/firmador';
  try {
    var data = { documentos: documentos };
    $.post(url,JSON.stringify(data), function(msg) {
      var respuesta = JSON.parse(msg);
      if(respuesta.Status){
        let timerInterval

        Swal.fire({
          icon: 'success',
          title: '??Documentos subidos con ??xito!',
          text: "El documento se ha firmado y subido al repositorio con ??xito.",
          html: 'Ser?? redirigido al inicio autom??ticamente en <strong></strong> segundos.<br/><br/>',
          timer: 3000,
          willOpen: () => {
          timerInterval = setInterval(() => {
              Swal.getContent().querySelector('strong')
              .textContent = (Swal.getTimerLeft() / 1000)
                  .toFixed(0)
          }, 100)
          },
          willClose: () => {
              clearInterval(timerInterval)
              window.location = '/firmador/web/document/uploaded';
          }
        });
      }
      else{
        Swal.fire({
          icon: 'error',
          title: 'ERROR: NO SE PUDO FIRMAR. ',
          html: '<big>' + respuesta.Message + '</big>' + '<br/>Ser?? redirigido al inicio autom??ticamente en <strong></strong> segundos.<br/><br/>',
          timer: 3000,
          willOpen: () => {
          timerInterval = setInterval(() => {
              Swal.getContent().querySelector('strong')
              .textContent = (Swal.getTimerLeft() / 1000)
                  .toFixed(0)
          }, 100)
          },
          willClose: () => {
              clearInterval(timerInterval)
              window.location = '/firmador/web/site/index';
          }
        });
      }
    }).fail(function() {
        Swal.fire({
          icon: "error",
          title: "ERROR: Servicio no encontrado",
          text: "No hay conectividad con el servicio de firma en su equipo. Por favor aseg??rese de haber instalado la aplicaci??n de firma localmente y de que el servicio est?? siendo ejecutando correctamente en la bandeja de tareas.",
        });
    });
  } catch (e) {
    console.log(e);
  }
}
