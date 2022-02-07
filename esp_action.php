<?php
    include_once('esp_database.php');

    $api_key_value = "tPmAT5Ab3j7F9";


    $action = $id = $name = $gpio = $state = $output_logic = $api_key =  "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = test_input($_POST["action"]);
        if ($action == "output_create") {
            $name = test_input($_POST["name"]);
            $gpio = test_input($_POST["gpio"]);
            $state = test_input($_POST["state"]);
            $result = createOutput($name, $gpio, $state);

            echo $result;
        }
        else if ($action == "output_logicCreate") {
            $out_gpio = test_input($_POST["out_gpio"]);
            $result = createOutputLogic($out_gpio);

            echo $result;
        }

        $api_key = test_input($_POST["api_key"]);   
        if($api_key == $api_key_value) {
            $id = test_input($_POST["id"]);
            $output_logic = test_input($_POST["output_logic"]);
            $result = inputData($id, $output_logic);

            echo $result;
        }
        else {
            echo "No data posted with HTTP POST.";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $action = test_input($_GET["action"]);
        if ($action == "outputs_state") {
            $result = getAllOutputStates();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[$row["gpio"]] = $row["state"];
                }
            }
            echo json_encode($rows);
        }
        else if ($action == "output_update") {
            $id = test_input($_GET["id"]);
            $state = test_input($_GET["state"]);
            $result = updateOutput($id, $state);
            echo $result;
        }
        else if ($action == "output_delete") {
            $id = test_input($_GET["id"]);
            $result = deleteOutput($id);
            echo $result;
        }
        else if ($action == "send_input") {
            $result = getIDandOutputGPIO();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[$row["id"]] = $row["out_gpio"];
                }
            }
            echo json_encode($rows);
        }
        else {
            echo "Invalid HTTP request.";
        }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
