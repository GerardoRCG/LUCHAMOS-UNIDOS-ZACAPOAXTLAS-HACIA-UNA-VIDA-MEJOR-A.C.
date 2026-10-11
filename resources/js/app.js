import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

// Solo inicializar Dropzone si el elemento #dropzone existe en la vista actual
if (document.querySelector("#dropzone")) {
    const dropzone = new Dropzone("#dropzone", {
        dictDefaultMessage: 'Sube aquí tu imagen',
        acceptedFiles: ".png, .jpg, .jpeg, .gif",
        addRemoveLinks: true,
        dictRemoveFile: 'Borrar Archivo',
        maxFiles: 1,
        uploadMultiple: false,

        init: function(){
            if(document.querySelector('[name="imagen"]').value.trim()) {
                const imagenPublicada = {}
                imagenPublicada.size = 1234;
                imagenPublicada.name = document.querySelector('[name="imagen"]').value;
                
                this.options.addedfile.call(this, imagenPublicada);
                this.options.thumbnail.call(this, imagenPublicada, `/uploads/${imagenPublicada.name}`)

                imagenPublicada.previewElement.classList.add("dz-success", "dz-complete")
            }
        },
    });

    // Evento que se dispara cuando la imagen se sube con éxito al servidor
    dropzone.on("success", function (file, response) {
        document.querySelector('[name="imagen"]').value = response.imagen;
    });

    // Si el usuario da clic en "Borrar Archivo" en Dropzone
    dropzone.on("removedfile", function () {
        document.querySelector('[name="imagen"]').value = "";
    });
}