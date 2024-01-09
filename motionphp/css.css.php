<?php
header("Content-type: text/css");

// Obtener las opciones del formulario
$imageWidth = $_GET["image-width"];
$imageHeight = $_GET["image-height"];
$transitionDuration = $_GET["transition-duration"];

// Estilos CSS del slider
echo "
.slider {
    width: {$imageWidth}px;
    height: {$imageHeight}px;
    overflow: hidden;
    position: relative;
}

.slider img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity {$transitionDuration}s ease-in-out;
    position: absolute;
    top: 0;
    left: 0;
}
";
?>