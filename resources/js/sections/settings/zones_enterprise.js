


let map;
let drawingManager;
let polygon;

if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}

// function getPoints(event, destination_id, zone_id){
//     event.preventDefault();
//     $("#zonesModal").modal("show");
//     $.ajax({
//         url: `/enterprises/destinations/${destination_id}/points`,
//         type: 'GET',
//         data: { zone_id: zone_id },
//         beforeSend: function() {        
//             $("#zone_map_container").empty().html(`<div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div>`)
//         },
//         success: function(resp) {
//             console.log(resp);            
//             initMap();
//             for (const key in resp) {
//                 if (resp.hasOwnProperty(key)) {
//                     let polygonCoords = [];
//                     const location = resp[key];
//                     location.points.forEach(point => {
//                         console.log(point);                        
//                         polygonCoords.push({ ui: Number(point.point_id), lat: Number(point.lat), lng: Number(point.lng) });
//                     });
                    
//                     var polygonData = new google.maps.Polygon({
//                         paths: polygonCoords,
//                         strokeColor: ((zone_id == location.id) ? '#1cbb8c' : '#FF0000'),
//                         strokeOpacity: 0.8,
//                         strokeWeight: 2,
//                         fillColor: ((zone_id == location.id) ? '#1cbb8c' : '#FF0000'),
//                         fillOpacity: 0.35
//                     });
                    
//                     polygonData.setMap(map);
                    
//                     // Agregar evento de clic para eliminar el polígono
//                     google.maps.event.addListener(polygonData, 'click', function(event) {
//                         console.log(polygonData, event);
                        
//                         Swal.fire({
//                             title: '¿Eliminar esta geocerca?',
//                             text: "Esta acción no se puede deshacer",
//                             icon: 'warning',
//                             showCancelButton: true,
//                             confirmButtonText: 'Sí, eliminar',
//                             cancelButtonText: 'Cancelar'
//                         }).then((result) => {
//                             if (result.isConfirmed) {
//                                 this.setMap(null); // Elimina el polígono                                
//                                 // Opcional: Eliminar también del servidor
//                                 $.ajax({
//                                     url: `/enterprises/destinations/${location.point_id}/points`,
//                                     type: 'DELETE',
//                                     success: function() {
//                                         Swal.fire(
//                                             'Eliminado!',
//                                             'La geocerca ha sido eliminada.',
//                                             'success'
//                                         );
//                                     }
//                                 }).fail(function() {
//                                     Swal.fire(
//                                         'Error',
//                                         'No se pudo eliminar la geocerca del servidor',
//                                         'error'
//                                     );
//                                 });
//                             }
//                         });
//                     });
//                 }
//             }
//             initDraw(zone_id);
//         },
//     }).fail(function(xhr, status, error) {
//         console.log(xhr);
//         Swal.fire(
//             '¡ERROR!',
//             xhr.responseJSON.message,
//             'error'
//         );
//     });
// }

function getPoints(event, destination_id, zone_id, enterprise_id) {
    event.preventDefault();
    $("#zonesModal").modal("show");
    $.ajax({
        url: `/enterprises/destinations/${destination_id}/points?enterprise_id=${enterprise_id}`,
        type: 'GET',
        data: { zone_id: zone_id },
        beforeSend: function() {        
            $("#zone_map_container").empty().html(`<div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div>`)
        },
        success: function(resp) {
            console.log(resp);            
            initMap();
            
            // Objeto para guardar la relación entre coordenadas y point_id
            const pointReferences = {};
            
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    let polygonCoords = [];
                    const location = resp[key];
                    
                    // Guardar todos los point_ids para esta geocerca
                    const geocercaPoints = [];
                    
                    location.points.forEach(point => {
                        console.log(point);
                        const coordKey = `${point.lat},${point.lng}`;
                        pointReferences[coordKey] = point.point_id;
                        geocercaPoints.push(point.point_id);
                        polygonCoords.push({ lat: Number(point.lat), lng: Number(point.lng) });
                    });
                    
                    var polygonData = new google.maps.Polygon({
                        paths: polygonCoords,
                        strokeColor: ((zone_id == location.id) ? '#1cbb8c' : '#FF0000'),
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: ((zone_id == location.id) ? '#1cbb8c' : '#FF0000'),
                        fillOpacity: 0.35
                    });
                    
                    // Almacenar los point_ids asociados a este polígono
                    polygonData.pointIds = geocercaPoints;
                    polygonData.zoneId = location.id;
                    polygonData.zoneName = location.name;
                    polygonData.destinationId = destination_id;
                    
                    polygonData.setMap(map);
                    
                    google.maps.event.addListener(polygonData, 'click', function(event) {
                        // Obtener el punto más cercano al click
                        const clickedPoint = this.getPath().getArray()
                            .map(p => ({lat: p.lat(), lng: p.lng()}))
                            .reduce((prev, curr) => {
                                const prevDist = google.maps.geometry.spherical.computeDistanceBetween(
                                    new google.maps.LatLng(prev.lat, prev.lng),
                                    new google.maps.LatLng(event.latLng.lat(), event.latLng.lng())
                                );
                                const currDist = google.maps.geometry.spherical.computeDistanceBetween(
                                    new google.maps.LatLng(curr.lat, curr.lng),
                                    new google.maps.LatLng(event.latLng.lat(), event.latLng.lng())
                                );
                                return currDist < prevDist ? curr : prev;
                            });
                        
                        const pointKey = `${clickedPoint.lat},${clickedPoint.lng}`;
                        const pointId = pointReferences[pointKey];
                        
                        Swal.fire({
                            title: '¿Eliminar este punto de la geocerca?',
                            html: `Geocerca con ID: <b>${pointId}</b><br>De la Zona: ${this.zoneName}`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                    
                                    $.ajax({
                                        url: `/enterprises/destinations/${location.id}/points`,
                                        type: 'DELETE',
                                        // data: { 
                                        //     new_path: updatedCoords,
                                        //     zone_id: this.zoneId 
                                        // },
                                        success: function() {
                                            Swal.fire({
                                                title: 'Eliminado!',
                                                icon: 'success',
                                                html: 'El punto ha sido eliminado de la geocerca',
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
                                    }).fail(function() {
                                        Swal.fire(
                                            'Error',
                                            'No se pudo eliminar el punto',
                                            'error'
                                        );
                                        // Revertir cambios visuales si falla
                                        polygonData.setPath(polygonCoords);
                                    });

                            }
                        });
                    });
                }
            }
            initDraw(zone_id);
        },
    }).fail(function(xhr, status, error) {
        Swal.fire('¡ERROR!', xhr.responseJSON.message, 'error');
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
      console.log(event, event.type, google.maps.drawing.OverlayType);        
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
                    url: `/enterprises/destinations/${zone_id}/points`,
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