function setStatus(event, type, status, item_id, rez_id){
    event.preventDefault();
    (async () => {
        // Crear un contenedor para Dropzone y el select
        const dropzoneContainer = document.createElement("div");
        const HTML = "";
        if( status == "CANCELLED" || status == "NOSHOW" ){
            HTML = `
                <label for="cancelReason">Selecciona el motivo de cancelación:</label>
                <select id="cancelReason" class="swal2-input">
                    <option value="">Seleccione una opción</option>
                    ${Object.entries(types_cancellations).map(([key, value]) => `<option value="${key}">${value}</option>`).join('')}
                </select>
                <label for="attachPicture">Debes adjuntar al menos una imagen:</label>
                <div id="dropzoneService" class="dropzone"></div>            
            `;
        }
        dropzoneContainer.classList.add('box_cancelation')        
        dropzoneContainer.innerHTML = `
            <p>${ status == "CANCELLED" || status == "NOSHOW" ? '¿Está seguro de cancelar la reservación? <br>  Esta acción no se puede revertir' : '¿Está seguro de actualizar el estatus? <br> Esta acción no se puede revertir' }</p>
            ${HTML}
        `;
        let selectedFiles = []; // Array para almacenar las imágenes seleccionadas


        const { isConfirmed, value } = await swal.fire({
            // title: "",
            html: dropzoneContainer,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar",
            allowOutsideClick: false,
            didOpen: () => {
                // Inicializar Dropzone
                new Dropzone("#dropzoneService", {
                    url: "/reservations/upload", // No se enviarán archivos aquí, solo los almacenaremos en memoria
                    maxFilesize: 5, // Tamaño máximo del archivo en MB
                    maxFiles: 5,
                    acceptedFiles: "image/*",
                    dictDefaultMessage: "Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...",
                    addRemoveLinks: true,
                    dictRemoveFile: "Eliminar imagen",
                    autoProcessQueue: false,
                    init: function () {
                        let dz = this;
                        dz.on("addedfile", function (file) {
                            selectedFiles.push(file);
                        });
    
                        dz.on("removedfile", function (file) {
                            selectedFiles = selectedFiles.filter(f => f !== file);
                        });
                    }
                });
            },
            preConfirm: () => {
                const reason = document.getElementById("cancelReason").value;
                const dropzone = Dropzone.forElement("#dropzone");
    
                if (!reason) {
                    Swal.showValidationMessage("Debes seleccionar un motivo de cancelación.");
                    return false;
                }
                if (dropzone.files.length === 0) {
                    Swal.showValidationMessage("Debes subir al menos una imagen.");
                    return false;
                }

                return { reason, images: dropzone.files };
                // return { reason, images: dropzone.files.map(file => file) };
            }            
        });

        if (isConfirmed) {
            const { reason, images } = value;
            console.log(value);
            
        }
    })();
}