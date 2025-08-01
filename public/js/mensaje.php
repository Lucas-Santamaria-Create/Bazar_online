<?php if (!empty($mensajeExito)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $mensajeExito; ?>',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php endif; ?>