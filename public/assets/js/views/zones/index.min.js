


let map;
let drawingManager;
let polygon;

$(function() {
    $('#zones_table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        }
    });    

    $('#btnSendZone').on('click', function (e) {        
        var destinationID = $("#destinationID").find("option:selected").val();
        window.location.href = '/config/destinations/' + destinationID;
    });

});


function getPoints(event, destination_id, zone_id){
    event.preventDefault();
    $("#zonesModal").modal("show");    

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 

    $.ajax({
        url: `/config/destinations/${destination_id}/points`,
        type: 'GET',
        data: { zone_id: zone_id },
        beforeSend: function() {        
            $("#zone_map_container").empty().html(`<div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div>`)
        },
        success: function(resp) {
            
            initMap();

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

function initMap(){
    // Opciones del mapa
    var mapOptions = {
        center: { lat: 21.0715784, lng: -86.870175 }, // Centro del mapa
        zoom: 11 // Zoom inicial
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

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });                
                $.ajax({
                    url: `/config/destinations/${zone_id}/points`,
                    type: 'PUT',
                    data: { coordinates },
                    beforeSend: function() {        
                        //$("#zones_table tbody").empty();
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
                        //$("#zones_table tbody").empty();
                        //$("#btn_qbtnSearchZonesuote").prop('disabled', false);
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