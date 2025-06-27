


let map;
let drawingManager;
let polygon;
// Coordenadas por destino
const DESTINATIONS = {
  1: { // Cancún
    lat: 21.161908, 
    lng: -86.851528,
    zoom: 11
  },
  2: { // Los Cabos
    lat: 22.890533, 
    lng: -109.916740,
    zoom: 11
  }
};


if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}

function getPoints(event, destination_id, zone_id){
    event.preventDefault();
    $("#zonesModal").modal("show");
    $.ajax({
        url: `/enterprises/destinations/web/${destination_id}/points`,
        type: 'GET',
        data: { zone_id: zone_id },
        beforeSend: function() {
            $("#zone_map_container").empty().html(`<div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div>`)
        },
        success: function(resp) {
            initMap(destination_id);
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {

                    let polygonCoords = [];
                    const location = resp[key];
                    location.points.forEach(point => {
                        polygonCoords.push( { lat: Number(point.lat), lng: Number(point.lng) } );
                    });
                    var polygonData = new google.maps.Polygon({
                        paths: polygonCoords,
                        strokeColor: (( zone_id == location.id )?'#1cbb8c':'#FF0000'),
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: (( zone_id == location.id )?'#1cbb8c':'#FF0000'),
                        fillOpacity: 0.35
                    });
                    polygonData.setMap(map);                    
                }
            }
            initDraw(zone_id);
        },
    }).fail(function(xhr, status, error) {
        console.log(xhr);
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
        //$("#zones_table tbody").empty();
        //$("#btn_qbtnSearchZonesuote").prop('disabled', false);
    });
}

function initMap(destination_id = 1){
    const defaultDestination = DESTINATIONS[destination_id]; // Cancún como valor por defecto

    // Opciones del mapa
    var mapOptions = {
        // center: { lat: 21.0715784, lng: -86.870175 }, // Centro del mapa
        center: { lat: defaultDestination.lat, lng: defaultDestination.lng }, // Centro del mapa
        zoom: defaultDestination.zoom // Zoom inicial
    };
    map = new google.maps.Map(document.getElementById('zone_map_container'), mapOptions);
}

function initDraw(zone_id) {
    // Configura el DrawingManager para dibujar polígonos
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        }
    });

    drawingManager.setMap(map);

    // Escucha el evento cuando se complete el dibujo del polígono
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
      if (event.type === google.maps.drawing.OverlayType.POLYGON) {
        polygon = event.overlay;
        const path = polygon.getPath();
        const coordinates = path.getArray().map(coord => ({
          lat: coord.lat(),
          lng: coord.lng()
        }));

        swal.fire({
            title: '¿Está seguro de guardar la Geocerca?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed == true){              
                $.ajax({
                    url: `/enterprises/destinations/web/${zone_id}/points`,
                    type: 'PUT',
                    data: { coordinates },
                    beforeSend: function() {        
                        $("#zone_map_container").empty().html(`<div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div>`);
                    },
                    success: function(resp) {                        
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'Geocerca actualizada con éxito. Será redirigido en <b></b>',
                            timer: 2500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = (Swal.getTimerLeft() / 1000)
                                        .toFixed(0)
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            location.reload();
                        })

                    }
                }).fail(function(xhr, status, error) {
                        console.log(xhr);
                        Swal.fire(
                            '¡ERROR!',
                            xhr.responseJSON.message,
                            'error'
                        );
                        $("#zonesModal").modal("hide");
                });
                console.log( coordinates );
            }else{                
                clearPolygon();
            }            
        });
      }
    });
}

function clearPolygon() {
    if (polygon) {
      polygon.setMap(null); // Elimina el polígono del mapa
      polygon = null; // Reinicia la variable del polígono
    }
}