var rango = new Array(0, 2.9, 3.9, 4.5, 5.0); // Las notas validas estan entre 1.0 y 5.0
var escala = new Array("", "Bajo", "Basico", "Alto", "Superior");
var formData = new FormData();
formData.append("curso", "210A");
$(function () {
  $("#fecha").datepicker();
  $("input[type=button]").button();
  $("#mensaje").dialog({
    autoOpen: false,
    buttons: {
      Ok: function () {
        $(this).dialog("close");
      },
    },
  });
  var html =
    '<table id="tabla1" border="1" align="center">' +
    "<tr><th>CODIGO</th><th>ALUMNO</th>";

  // número máximo de notas
  let maxNotas = 0;
  $.each(CodsAlum, function (idalu, codalu) {
    if (typeof Notas[codalu] != "undefined") {
      const numNotas = Object.keys(Notas[codalu]).length - 1; //Sin el promedio
      if (numNotas > maxNotas) {
        maxNotas = numNotas;
      }
    }
  });

  // Crear los encabezados dinámicamente
  for (let i = 1; i <= maxNotas; i++) {
    html += `<th>Nota ${i}</th>`;
  }
  html += "<th>Promedio</th><th>Desempeño</th></tr>";

  // Generar las filas de la tabla
  $.each(CodsAlum, function (idalu, codalu) {
    html += `<tr data-cod="${codalu}"><td align="center">${codalu}</td>`;
    html += `<td>${Alumnos[codalu]}</td>`;

    // Crear inputs dinámicamente para las notas
    for (let i = 1; i <= maxNotas; i++) {
      const nota =
        typeof Notas[codalu] != "undefined" && Notas[codalu][i]
          ? Notas[codalu][i]
          : "";
      const input = `<input type="text" id="nota${i}_${codalu}" value="${nota}" size="3" class="entrada">`;
      html += `<td>${input}</td>`;
    }

    // Agregar promedio y desempeño
    const final =
      typeof Notas[codalu] != "undefined" && Notas[codalu][0]
        ? Notas[codalu][0]
        : "";
    const inputFinal = `<input type="text" id="X${codalu}" value="${final}" size="3" class="salida" readonly>`;
    html += `<td align="center">${inputFinal}</td>`;
    html += `<td><div id="W${codalu}">${Desempeno(final)}</div></td></tr>`;
  });

  html += "</table>";
  $("#contenedor").html(html);

  $(document).on("change", ".entrada", function () {
    CambiarColor($(this).attr("id"), $(this).val());
  });
  $("#masCol").click(function () {
    AdicionarColumna();
  });
  $("#grabar").click(function () {
    GrabarNotas();
  });
});
function CambiarColor(id, nota) {
  var CDesemp = "W" + id.substr(1, 5);
  var CFinal = "X" + id.substr(1, 5);
  if (nota <= rango[4]) {
    formData.append(id, nota);
    $("#" + CDesemp).html(Desempeno(nota));
    $("#" + CFinal).val(nota);
  }
  if (nota <= rango[1]) $("#" + id).css("color", "red");
  else if (nota <= rango[2]) $("#" + id).css("color", "orange");
  else if (nota <= rango[3]) $("#" + id).css("color", "green");
  else if (nota <= rango[4]) $("#" + id).css("color", "blue");
  else $("#" + id).val("");
}
function Desempeno(nota) {
  if (nota == "") return "";
  if (nota <= rango[1]) return "Bajo";
  if (nota <= rango[2]) return "Básico";
  if (nota <= rango[3]) return "Alto";
  if (nota <= rango[4]) return "Superior";
}
function AdicionarColumna() {
  const ultimaNota = $("#tabla1 tbody th:contains('Nota')").last();
  const nuevaNotaIndex = $("#tabla1 tbody th:contains('Nota')").length + 1; // El nuevo índice para la columna de nota
  // Generar el ID de la nueva columna (Nota 3, Nota 4, etc.)
  const nuevaColumnaID = `Nota ${nuevaNotaIndex}`;

  if (nuevaNotaIndex < 8) {
    ultimaNota.after(`<th>${nuevaColumnaID}</th>`);

    $("#tabla1 tbody tr").each(function () {
      const codalu = $(this).data("cod");
      const nuevoInput = `<input type="text" id="nota${nuevaNotaIndex}_${codalu}" value="" size="3" class="entrada">`;
      // Insertar la nueva celda después de la última columna de nota
      $(this)
        .children("td")
        .eq(ultimaNota.index())
        .after(`<td>${nuevoInput}</td>`);
    });
  } else {
    alert("Has alcanzado el máximo de notas permitidas");
  }
}

function GrabarNotas() {
  const datos = [];
  let valid = true;

  // Recorre cada fila de la tabla para recopilar las notas
  // ignoro la primera fila porque es el encabezado y no tiene datos
  $("#tabla1 tbody tr:not(:first-child)").each(function () {
    const codalu = $(this).data("cod");
    const notas = [];

    $(this)
      .find("input.entrada")
      .each(function () {
        const nota = $(this).val();
        const id = $(this).attr("id");
        // Validar que la nota sea un número entre 0 y 5

        notas.push({ id: id, valor: nota });
      });

    datos.push({ codigo: codalu, notas: notas }); // Agregar datos del alumno
  });

  // Enviar datos al servidor mediante AJAX
  $.ajax({
    url: "guardar_notas.php",
    method: "POST",
    data: { alumnos: JSON.stringify(datos) },
    success: function (resp) {
      alert("Notas guardadas correctamente: " + resp);
    },
    error: function (xhr, status, error) {
      console.error("Error al guardar las notas: ", error);
      alert("Error al guardar las notas. ", error);
    },
  });
}
$(document).ready(function () {
  $("#tabla1").on("keydown", "input.entrada", function (e) {
    let $this = $(this);
    let $nextInput;

    switch (e.key) {
      case "ArrowRight":
        $nextInput = $this.closest("td").next().find("input");
        break;
      case "ArrowLeft":
        $nextInput = $this.closest("td").prev().find("input");
        break;
      case "ArrowDown":
        $nextInput = $this.closest("tr").next().find("input").first();
        break;
      case "ArrowUp":
        $nextInput = $this.closest("tr").prev().find("input").first();
        break;
      case "Enter":
        $nextInput = $this.closest("td").next().find("input").length
          ? $this.closest("td").next().find("input")
          : $this.closest("tr").next().find("input").first();
        e.preventDefault();
        break;
    }

    $nextInput.focus();
  });
});
