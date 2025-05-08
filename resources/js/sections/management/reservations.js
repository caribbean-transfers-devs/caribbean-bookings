document.addEventListener("DOMContentLoaded", function() {
    // Intervalo en milisegundos (2.5 minutos = 150,000 ms)
    const intervalo = 150000;
    // Configuración
    const config = {
        intervaloReload: 150000, // 2.5 minutos en milisegundos
        tiempoEsperaRespuesta: 30000, // 30 segundos para esperar respuesta
        mostrarNotificacionAntes: 60000 // 1 minuto antes de recargar
    };

    // Variables de estado
    let intervaloPrincipal;
    let temporizadorConfirmacion;
    let recargaPendiente = false;

    // Elementos de la interfaz
    let notificacion;
    let contadorElement;
    
    if ( document.getElementById('lookup_date') != null ) {
        const picker = new easepick.create({
            element: "#lookup_date",
            css: [
                'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
            ],
            zIndex: 10,
        });
    }

    const tablesItem = document.querySelectorAll('.table-rendering');
    // if( tablesItem.length > 0 ){
        components.actionTable($('.table-bookings'), 'fixedheader');
        components.actionTable($('.table-arrivals'), 'fixedheader');
        components.actionTable($('.table-departures'), 'fixedheader');
    // }
    components.formReset();

    components.titleModalFilter();
    components.renderCheckboxColumns('dataBookings', 'columns');
    components.setValueSelectpicker();

    // Función para recargar la página
    // function reloadPage() {
    //     console.log('Recargando página...', new Date().toLocaleTimeString());
    //     window.location.reload();
    // }

    // Configurar el intervalo
    // console.log('Iniciando auto-recarga cada 2.5 minutos...');
    // const intervaloId = setInterval(reloadPage, intervalo);

    function inicializarAutoRecarga() {
        console.log(`Iniciando auto-recarga cada ${config.intervaloReload/60000} minutos`);
        
        intervaloPrincipal = setInterval(() => {
            solicitarConfirmacionRecarga();
        }, config.intervaloReload);
    }

    function crearNotificacion() {
        // Crear elementos HTML para la notificación
        notificacion = document.createElement('div');
        notificacion.style.position = 'fixed';
        notificacion.style.bottom = '20px';
        notificacion.style.right = '20px';
        notificacion.style.backgroundColor = '#f8f9fa';
        notificacion.style.border = '1px solid #dee2e6';
        notificacion.style.borderRadius = '5px';
        notificacion.style.padding = '15px';
        notificacion.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';
        notificacion.style.zIndex = '1000';
        notificacion.style.maxWidth = '300px';
        
        contadorElement = document.createElement('div');
        contadorElement.style.marginBottom = '10px';
        contadorElement.style.fontWeight = 'bold';
        
        const mensaje = document.createElement('p');
        mensaje.textContent = 'La página se recargará automáticamente para mantener los datos actualizados. ¿Desea continuar?';
        mensaje.style.marginBottom = '15px';
        
        const btnAceptar = document.createElement('button');
        btnAceptar.textContent = 'Aceptar';
        btnAceptar.style.marginRight = '10px';
        btnAceptar.style.padding = '5px 10px';
        btnAceptar.style.backgroundColor = '#28a745';
        btnAceptar.style.color = 'white';
        btnAceptar.style.border = 'none';
        btnAceptar.style.borderRadius = '3px';
        btnAceptar.style.cursor = 'pointer';
        
        const btnCancelar = document.createElement('button');
        btnCancelar.textContent = 'Cancelar';
        btnCancelar.style.padding = '5px 10px';
        btnCancelar.style.backgroundColor = '#dc3545';
        btnCancelar.style.color = 'white';
        btnCancelar.style.border = 'none';
        btnCancelar.style.borderRadius = '3px';
        btnCancelar.style.cursor = 'pointer';
        
        // Agregar elementos al contenedor
        notificacion.appendChild(contadorElement);
        notificacion.appendChild(mensaje);
        notificacion.appendChild(btnAceptar);
        notificacion.appendChild(btnCancelar);
        
        // Agregar al documento
        document.body.appendChild(notificacion);
        
        // Event listeners
        btnAceptar.addEventListener('click', () => {
            clearTimeout(temporizadorConfirmacion);
            recargarPagina();
        });
        
        btnCancelar.addEventListener('click', () => {
            clearTimeout(temporizadorConfirmacion);
            ocultarNotificacion();
            recargaPendiente = false;
        });
    }

    function actualizarContador(segundos) {
        if (contadorElement) {
            contadorElement.textContent = `La página se recargará en ${segundos} segundos...`;
        }
    }
    
    function ocultarNotificacion() {
        if (notificacion && notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }
    
    function solicitarConfirmacionRecarga() {
        if (recargaPendiente) return;
        
        recargaPendiente = true;
        crearNotificacion();
        
        let segundosRestantes = config.tiempoEsperaRespuesta / 1000;
        actualizarContador(segundosRestantes);
        
        temporizadorConfirmacion = setInterval(() => {
            segundosRestantes -= 1;
            actualizarContador(segundosRestantes);
            
            if (segundosRestantes <= 0) {
                clearInterval(temporizadorConfirmacion);
                recargarPagina();
            }
        }, 1000);
    }
    
    function recargarPagina() {
        console.log('Recargando página...', new Date().toLocaleTimeString());
        ocultarNotificacion();
        window.location.reload();
    }
    
    // Iniciar cuando el DOM esté listo
    inicializarAutoRecarga();
    
    // Opcional: Detener la recarga automática si es necesario
    window.detenerAutoRecarga = function() {
        clearInterval(intervaloPrincipal);
        clearTimeout(temporizadorConfirmacion);
        ocultarNotificacion();
        recargaPendiente = false;
        console.log('Auto-recarga detenida');
    };
    
    // Opcional: Detener la recarga automática bajo ciertas condiciones
    // Ejemplo: si hay un formulario con cambios no guardados
    // window.stopAutoRecharge = function() {
    //     clearInterval(intervaloId);
    //     console.log('Auto-recarga detenida');
    // };
    
    // Opcional: Recargar inmediatamente si se necesita
    // window.reloadNow = function() {
    //     reloadPage();
    // };    
});