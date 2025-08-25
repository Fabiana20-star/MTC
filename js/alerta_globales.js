 const inputFoto = document.getElementById('upload-foto');
  const previewFoto = document.getElementById('preview-foto');

  // Cargar imagen guardada si existe
  window.addEventListener('DOMContentLoaded', () => {
    const imagenGuardada = localStorage.getItem('fotoPerfil');
    if (imagenGuardada) {
      previewFoto.src = imagenGuardada;
    }
  });

  // Cambiar imagen y guardarla
  inputFoto.addEventListener('change', function () {
    const archivo = this.files[0];
    if (archivo) {
      const lector = new FileReader();
      lector.onload = function (e) {
        previewFoto.src = e.target.result;
        localStorage.setItem('fotoPerfil', e.target.result); // Guardar en navegador
      }
      lector.readAsDataURL(archivo);
    }
  });