<!--
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com/control-esp32-esp8266-gpios-from-anywhere/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
-->
<?php
    include_once('esp_database.php');

    $result = getAllOutputs();
    $html_buttons= $html_state = $html_delete= $html_grid = null;
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row["state"] == "1"){
                $button_checked = "checked";
            }
            else {
                $button_checked = "";
            }
            // $html_buttons .= '<h3>' . $row["name"] . ' - GPIO ' . $row["gpio"] . ' </h3>
            // <label class="switch"><input type="checkbox" onchange="updateOutput(this)" id="' . $row["id"] . '" ' . $button_checked . '>
            // <span class="slider"></span></label>';
            // $html_state .= $row["state"];
            // $html_delete .= '<h3> <i><a onclick="deleteOutput(this)" href="javascript:void(0);" id="'. $row["id"] . '">Delete</a></i></h3>';

          $html_grid .= '<div class="grid-item"><h3>' . $row["name"] . ' - GPIO ' . $row["gpio"] . ' </h3>
            <label class="switch"><input type="checkbox" onchange="updateOutput(this)" id="' . $row["id"] . '" ' . $button_checked . '>
            <span class="slider"></span></label></div><div class="grid-item">'.$row["state"].'</div>
          <div class="grid-item"><h3> <i><a onclick="deleteOutput(this)" href="javascript:void(0);" id="'. $row["id"] . '">Delete</a></i></h3></div>'; 
        }
    }

?>

<!DOCTYPE HTML>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="stylesheet" type="text/css" href="esp_style.css"> -->
        <title>ESP Output Control</title>
        <style>
            .item1 { grid-area: header; }
            .item2 { grid-area: form; }
            .item3 { grid-area: switch; }
            html {
                font-family: Arial;
               /* display: inline-block;
                text-align: center;*/
            }
            form{
                border-radius: 4px;
                background-color: #efefef;
                padding: 20px;
                max-width: 300px;
                margin:0px auto;
                padding-bottom: 25px;
            }

            /*h2 {
                font-size: 3.0rem;
            }*/
            .grid-layout {
              display: grid;
              grid-template-areas:
                'header header header header header header'
                'form form switch switch switch switch';
              grid-gap: 10px;
              /*background-color: #2196F3;*/
              padding: 10px;
            }
            .grid-container {
                border-radius: 4px;
                background-color: #efefef;
                padding: 20px;
              display: grid;
              grid-template-columns: auto auto auto;
              /*background-color: #2196F3;*/
              padding: 10px;
            }
            .grid-item {
              /*background-color: rgba(255, 255, 255, 0.8);*/
              /*border: 1px solid rgba(0, 0, 0, 0.8);*/
              padding: 20px;
              font-size: 20px;
              text-align: center;
            }
            .grid-layout > div {
              /*background-color: rgba(255, 255, 255, 0.8);*/
              text-align: center;
              padding: 20px 0;
              font-size: 30px;
            }
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .switch input {
                display: none
            }

            .slider {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #949494;
                border-radius: 34px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px; bottom: 4px;
                background-color: #fff;
                -webkit-transition: .4s;
                transition: .4s;
                border-radius: 68px;
            }

            input:checked+.slider {
                background-color: #008B74;
            }

            input:checked+.slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }
            input[type=text], input[type=number], select {
                width: 100%;
                padding: 6px 10px;
                margin: 8px 0;
                display: inline;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }

            input[type=submit] {
                width: 100%;
                background-color: #008B74;
                color: white;
                padding: 7px 10px;
                margin: 8px 0;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            input[type=submit]:hover {
                background-color: #005a4c;
            }
        </style>
    </head>

<body>
    <div class="grid-layout">
        <div class="item1"><h2>ESP Output Control</h2></div>
        <div class="item2">
            
                <form onsubmit="return createOutput();">
                <h3>Create New Input</h3>
                <label for="outputName">Name</label>
                <input type="text" name="name" id="outputName"><br>
                <label for="outputGpio">GPIO Number</label>
                <input type="number" name="gpio" min="0" id="outputGpio">
                <label for="outputState">Initial GPIO State</label>
                <select id="outputState" name="state">
                  <option value="0">0 = OFF</option>
                  <option value="1">1 = ON</option>
                </select>
                <input type="submit" value="Create Input">
                </form>
                <form onsubmit="return createOutputLogic();">
                <h3>Create New Output</h3>
                <label for="out_gpio">Output GPIO Number</label>
                <input type="number" name="out_gpio" min="0" id="out_gpio"><br>
                <input type="submit" value="Create Output">
                <p><strong>Note:</strong> in some devices, you might need to refresh the page to see your newly created buttons or to remove deleted buttons.</p>
                </form>
            
        </div>
        <div class="item3">
            <div  class="grid-container">
                <?php echo $html_grid; ?>
            </div>
        </div>



    </div>


    <script>
        function updateOutput(element) {
            var xhr = new XMLHttpRequest();
            if(element.checked){
                xhr.open("GET", "esp_action.php?action=output_update&id="+element.id+"&state=1", true);
            }
            else {
                xhr.open("GET", "esp_action.php?action=output_update&id="+element.id+"&state=0", true);
            }
            xhr.send();
        }

        function deleteOutput(element) {
            var result = confirm("Want to delete this output?");
            if (result) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "esp_action.php?action=output_delete&id="+element.id, true);
                xhr.send();
                alert("Output deleted");
                setTimeout(function(){ window.location.reload(); });
            }
        }

        function createOutput(element) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "esp_action.php", true);

            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    alert("Output created");
                    setTimeout(function(){ window.location.reload(); });
                }
            }
            var outputName = document.getElementById("outputName").value;
            var outputGpio = document.getElementById("outputGpio").value;
            var outputState = document.getElementById("outputState").value;
            var httpRequestData = "action=output_create&name="+outputName+"&gpio="+outputGpio+"&state="+outputState;
            xhr.send(httpRequestData);
        }

        function createOutputLogic(element) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "esp_action.php", true);

            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    alert("Output Gate created");
                    setTimeout(function(){ window.location.reload(); });
                }
            }
            var out_gpio = document.getElementById("out_gpio").value;
            var httpRequestData = "action=output_logicCreate&out_gpio="+out_gpio;
            xhr.send(httpRequestData);
        }


    </script>
</body>
</html>
