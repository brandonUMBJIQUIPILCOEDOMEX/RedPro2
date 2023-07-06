<?php echo "dalee";



?>




<head>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.js" integrity="sha256-DrT5NfxfbHvMHux31Lkhxg42LY6of8TaYyK50jnxRnM=" crossorigin="anonymous"></script>


    <script src="https://cdn.socket.io/3.1.1/socket.io.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">








    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let numeroPuntos = 0;

            var realValueText = document.getElementById("realValueText");
            //Svar realValuePercentage = document.getElementById("realValuePercentage");

            var socket = io.connect('http://localhost:5000');

            socket.on('data', function(data) {
                console.log("ajuste" + data.ajuste);
                console.log("real" + data.real);

                realValueText.textContent = "" + data.real;
                realValuePercentage.textContent = "%";

                chart.options.data[0].dataPoints.push({
                    y: parseFloat(data.ajuste)
                });
                chart.options.data[1].dataPoints.push({
                    y: parseFloat(data.real)
                });
                chart.render();
            });

            let chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "HUMEDAD TIEMPO REAL & A"
                },
                data: [{

                    name: "Real",
                    type: "line",
                    indexLabelFontSize: 16,
                    dataPoints: []
                }, {
                    name: "Ajuste",
                    type: "line",
                    indexLabelFontSize: 16,
                    dataPoints: []
                }]
            });
            chart.render();




            // control del sistema

            // Agregar evento al botón de encender LED
            $("#btnOn").click(function() {
                $.post('http://localhost:5000/led', {
                    command: 'ON'
                }); // Enviar solicitud POST al servidor Flask
            });

            // Agregar evento al botón de apagar LED
            $("#btnOff").click(function() {
                $.post('http://localhost:5000/led', {
                    command: 'OFF'
                }); // Enviar solicitud POST al servidor Flask
            });




            var saveButton = document.getElementById("saveButton");
            saveButton.innerHTML = "Guardar imagen";
            saveButton.onclick = function() {
                chart.exportChart({
                    format: "png"
                });
            };
        });


        function copyText() {
            var element = document.getElementById("realValueText");
            var textToCopy = 'Humedad tiempo real: ' + element.innerText + '%';

            navigator.clipboard.writeText(textToCopy).then(function() {
                var notification = document.createElement('div');
                notification.classList.add('alert', 'alert-success');
                notification.innerHTML = 'El contenido se ha copiado al portapapeles';
                document.body.appendChild(notification);

                setTimeout(function() {
                    document.body.removeChild(notification);
                }, 3000);
            }, function(error) {
                console.error("Error al copiar el contenido: ", error);
            });
        }
    </script>








</head>

<body>



    <div class="container">
        <div class="row justify-content-center">
            <div class="col-8 p-4" style="margin-top: 50px;">
                <div id="chartContainer" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!--<div id="chartContainer" style="height: 300px; width: 100%;"></div> -->




    <!-- <span id="ajusteValue"></span>  -->
    <span id="realValuePercentage"></span>





    <!--
	<button id="saveButton">Guardar g</button>
	-->




    <div class="mb-3 text-center">
        <h2><span class="">Humedad : </span> <span id="realValueText" class="text-primary">0 </span><span id="realValuePercentage" class="text-primary">%</span></h2>
        <button type="button" class="btn btn-warning mt-5" id="saveButton">Guardar</button>
        <button class="btn btn-primary mt-5" onclick="copyText()">Copiar %</button>
        <a href="?" class="btn btn-warning mt-5">Back</a>

        <!-- Agregar botones de encender y apagar el LED -->
        <button id="btnOn" class="btn btn-primary mt-5">Encender LED</button>
        <button id="btnOff" class="btn btn-primary mt-5">Apagar LED</button>
    </div>


    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


</body>