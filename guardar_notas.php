<?php
$link = mysqli_connect("localhost", "root", "", "examen24");
if (!$link) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener datos enviados por AJAX
$data = json_decode($_POST['alumnos'], true);
// var_dump($data);

foreach ($data as $alumno) {
    $codigo = $alumno['codigo'];
    foreach ($alumno['notas'] as $nota) {
        $id = $nota['id']; 
        $valor = $nota['valor'];

        // Validar que sea un número y esté dentro del rango
        if (!is_numeric($valor) || $valor < 0 || $valor > 5) {
            // Mensaje de error detallado
            echo json_encode(["error" => "La nota $valor no es válida para el código $codigo. Debe ser un número entre 0 y 5."]);
            exit(); // Termina el script si el valor no es válido
        } 

        $columna = explode('_', $id)[0]; 
        
        $consulta = "SELECT * FROM notasa24 WHERE codigo = '$codigo'";
        $resultado = mysqli_query($link, $consulta);
        if (mysqli_num_rows($resultado) > 0) {
            $update = "UPDATE notasa24 SET $columna = $valor WHERE codigo = '$codigo'";
            mysqli_query($link, $update) or die("Error al actualizar: " . mysqli_error($link));
        } else {
            $insert = "INSERT INTO notasa24 (codigo, $columna) VALUES ('$codigo', $valor)";
            mysqli_query($link, $insert) or die("Error al insertar: " . mysqli_error($link));
        }
    }
}

// echo json_encode(["success" => true, "message" => "Notas guardadas correctamente."]);
mysqli_close($link);
?>
