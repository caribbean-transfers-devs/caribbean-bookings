(async () => {
    let dropzoneInstance;
    let selectedFiles = []; // Array para almacenar las imágenes seleccionadas

    const dropzoneContainer = document.createElement("div");
    dropzoneContainer.innerHTML = `
    <p>¿Está seguro de cancelar la reservación? <br>  Esta acción no se puede revertir</p>   
        <label for="cancelReason">Selecciona el motivo de cancelación:</label>
        <select id="cancelReason" class="swal2-input">
            <option value="">Seleccione una opción</option>
            ${Object.entries(types_cancellations).map(([key, value]) => `<option value="${key}">${value}</option>`).join('')}
        </select>
        <label for="attachPicture">Debes adjuntar al menos una imagen:</label>
        <div id="dropzone" class="dropzone"></div>
    `;

    const { isConfirmed, value } = await Swal.fire({
        // title: "",
        html: dropzoneContainer,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: () => {
            // Inicializar Dropzone
            dropzoneInstance = new Dropzone("#dropzone", {
                url: "/reservations/upload", // No se enviarán archivos aquí, solo los almacenaremos en memoria
                maxFilesize: 5, // Tamaño máximo del archivo en MB
                maxFiles: 5,
                acceptedFiles: "image/*",
                dictDefaultMessage: "Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...",
                addRemoveLinks: true,
                dictRemoveFile: "Eliminar",
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

            if (!reason) {
                Swal.showValidationMessage("Debes seleccionar un motivo de cancelación.");
                return false;
            }
            if (selectedFiles.length === 0) {
                Swal.showValidationMessage("Debes subir al menos una imagen.");
                return false;
            }

            return { reason, images: selectedFiles };
        }
    });

    if (isConfirmed) {
        const { reason, images } = value;

        Swal.fire({
            title: "Subiendo imágenes...",
            text: "Por favor, espera mientras se cargan las imágenes.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const uploadedImages = await uploadImages(images);

            if (uploadedImages.length === images.length) {
                Swal.fire({
                    title: "Confirmando cancelación...",
                    text: "Procesando la cancelación de la reservación.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append("type", reason);
                uploadedImages.forEach((imgUrl, index) => {
                    formData.append(`images[${index}]`, imgUrl);
                });

                await components.request_exec_ajax(_LOCAL_URL + "/reservations/" + id, "DELETE", formData);

                Swal.fire("Reservación cancelada", "La reservación se ha cancelado con éxito.", "success");
            } else {
                Swal.fire("Error en la subida", "Algunas imágenes no se pudieron subir. Intenta de nuevo.", "error");
            }
        } catch (error) {
            Swal.fire("Error", "Ocurrió un problema al subir las imágenes. Inténtalo nuevamente.", "error");
        }
    }
})();

// 🔹 Función para subir imágenes de manera independiente
async function uploadImages(files) {
    const uploadedImages = [];
    for (const file of files) {
        const formData = new FormData();
        formData.append("image", file);

        try {
            const response = await fetch(_LOCAL_URL + "/upload-image", {
                method: "POST",
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                uploadedImages.push(data.imageUrl);
            } else {
                throw new Error("Error al subir una imagen.");
            }
        } catch (error) {
            console.error("Error subiendo imagen:", error);
            return [];
        }
    }
    return uploadedImages;
}
