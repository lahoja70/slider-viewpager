<?php
header("Content-type: application/javascript");

// Obtener las opciones del formulario
$imageWidth = $_POST["image-width"];
$imageHeight = $_POST["image-height"];
$transitionDuration = $_POST["transition-duration"];

// Código JavaScript para actualizar el estilo del slider
echo "
document.addEventListener('DOMContentLoaded', function() {
    var sliderImage = document.getElementById('slider-image');
    sliderImage.style.width = '{$imageWidth}px';
    sliderImage.style.height = '{$imageHeight}px';
    sliderImage.style.transitionDuration = '{$transitionDuration}s';

    // Resto del código JavaScript aquí
});
";
?>