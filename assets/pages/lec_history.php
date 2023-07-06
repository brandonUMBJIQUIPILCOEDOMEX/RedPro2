<?php global $user; ?>

<html lang="es">

<head>
    <!-- Tus otros elementos en el head -->

    <script>
    function copyRow(button) {
        var row = button.parentNode.parentNode;
        var cells = row.getElementsByTagName('td');
        var rowData = '';

        rowData += 'Humedad del cultivo: ' + cells[2].innerText + '%'+'\n';
        rowData += 'Hora: ' + cells[3].innerText + '\n';
        rowData += 'Fecha: ' + cells[4].innerText;

        // Copiar al portapapeles
        navigator.clipboard.writeText(rowData);

        // Mostrar notificación de Bootstrap
        var notification = document.createElement('div');
        notification.classList.add('alert', 'alert-success');
        notification.innerHTML = 'Contenido de la fila copiado al portapapeles.';
        document.body.appendChild(notification);

        // Remover la notificación después de 3 segundos
        setTimeout(function() {
            document.body.removeChild(notification);
        }, 3000);
    }
</script>

</head>
<div class=" container mt-5 p-4"></div>
<h1 class="text-center">Historial de Lecturas</h1>
<div class="container">
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th scope="col"> ID usuario</th>
                <th scope="col"> ID Lectura</th>
                <th scope="col"> Humedad %</th>
                <th scope="col"> Hora</th>
                <th scope="col"> Fecha</th>
                <!--  <th scope="col"> Eliminar</th> -->
                <th scope="col"> Publicar</th>



            </tr>
        </thead>
        <tbody>


            <?php



            // Llamar a la función showReads() para obtener los resultados
            $results = showReads();
            // tremos el id del usuario actual con sesion
            $user_id = $_SESSION['userdata']['id'];

            // Recorrer los resultados con foreach
            foreach ($results as $row) {
                $id_lect = $row['id_lectura'];
                $phumedad = $row['phumedad'];
                $fecha = $row['fecha'];
                $hora = $row['hora'];

                echo "<tr>";
                echo "<td>{$user_id}</td>";
                echo "<td>{$id_lect}</td>";
                echo "<td>{$phumedad}</td>";
                echo "<td>{$hora}</td>";
                echo "<td>{$fecha}</td>";
                echo "<td><button class='btn btn-light' onclick='copyRow(this)'>Copiar</button></td>";
                echo "</tr>";
            }

            ?>
















        </tbody>
    </table>
</div>



</tbody>
</table>
</div>

<!-- a BACK Button to go to pervious page -->
<div class="container text-center mt-5">
    <a href="?" class="btn btn-warning mt-5"> Regresar </a>
    <div class="mb-3"></div>
    <div>