<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Exámen de Prueba</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="ajax.js"></script>

<?php
$Frases=array('Esperamos que pueda suceder cualquier cosa, y nunca estamos prevenidos para nada. Sophie Soynonov',
'El pasado es como una lámpara colocada a la entrada del porvenir. Félicité Robert de Lamennais',
'Valor es lo que se necesita para levantarse y hablar, pero también es lo que se requiere para sentarse y escuchar.',
'Si no sueltas el pasado, ¿con qué mano agarras el futuro?');
$curso="210A";
$codcol="00026011";
$alucur="alucura24";
$archnotas='notasa24';
$link = mysqli_connect("localhost", "root", "", "examen24");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
mysqli_select_db($link, "examen24");

$consulta = "SELECT * FROM $archnotas WHERE curso='$curso'";
$result = mysqli_query($link, $consulta) or die ("* ERROR EN $archnotas *". mysqli_error($link));
$Alumnos=array();
while ($registro = mysqli_fetch_array($result)) {
	$codal=$registro['codigo'];
	for ($i = 1; $i <= 7; $i++) { 
		$nota = $registro['nota' . $i];
		if ($nota != 0) {
				$Notas[$codal][$i] = $nota;  // Guardo solo las notas que no son 0
		}
	}
	// Calculo el promedio
	$Notas[$codal][0] = round(array_sum($Notas[$codal]) / count($Notas[$codal]), 1);

}

$consulta = "SELECT C.codigo,A.apellidos,A.nombres FROM $alucur C,alumnos A ";
$consulta.= " WHERE C.curso='$curso' AND C.codigo=A.codigo ORDER BY A.apellidos ASC ";
$result = mysqli_query($link, $consulta) or die ("* ERROR EN ALUMNOS *". mysqli_error($link));
$Alumnos=array();
while ($registro = mysqli_fetch_array($result)) {
	$codal=$registro['codigo'];
	$Alumnos[$codal]=utf8_encode($registro['apellidos'].' '.$registro['nombres']);
}
$n = rand(0,3);
$CodsAlum=array_keys($Alumnos); // Guardo el codigo del alumno para insertar las columnas
mysqli_close($link);

?>
<script>Alumnos=<?PHP echo json_encode($Alumnos);?></script>
<script>CodsAlum=<?PHP echo json_encode($CodsAlum);?></script>
<script>Notas=<?PHP echo json_encode($Notas);?></script>
</head>

<body>
<center>
<div id="principal">
<br />
<h1>COLEGIO DE PRUEBA SYSCOLEGIOS </h1>
<marquee>
<?php echo $Frases[$n]. " ".date('Y-m-d'); ?>
</marquee>
<div class="header">
	<h2>PLANILLA DE INGRESO DE CALIFICACIONES</h2>
	<button id="change-color" class="btn-theme">Tema claro</button>
</div>
<p>Fecha de Ingreso: 
<input type="text" id="fecha" readonly="readonly" size="8" class="fecha" /></p>
<hr />
<img src="SignoMas.jpg" id="masCol" class="add-column" title="Adicionar Columna" />
<form id="Form1">
<div id="contenedor"></div>
</form>
<div id="mensaje" title="Mensaje syscolegios"></div>
<hr />

<input type="button" id="grabar" value="Grabar">
<input type="button" id="regresar" value="Regresar" onclick="window.history.go(-1);">
<hr />
</div>
</center>

<script>
     const boton = document.getElementById('change-color');

    // Función para cambiar los colores
    boton.addEventListener('click', function() {
      const currentBackground = getComputedStyle(document.documentElement).getPropertyValue('--background').trim();

      if (currentBackground === '#2b2a2a') {
				boton.innerHTML = 'Tema oscuro';
        document.documentElement.style.setProperty('--texto', '#000');
        document.documentElement.style.setProperty('--background', '#fff');
      } else {
				boton.innerHTML = 'Tema claro';
        document.documentElement.style.setProperty('--texto', 'rgb(214, 214, 214)');
        document.documentElement.style.setProperty('--background', '#2b2a2a');
      }
    });
  </script>
</body>
</html>