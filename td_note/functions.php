<?php

$shapes = [
    "trapeze" => function (float $sB, float $b, float $h) {
        return ($sB+$b) * $h / 2;
    },
    "losange" => function (float $sDiagonal, float $diagonal) {
        return $sDiagonal * $diagonal / 2;
    }
];

function getData(string $val): string {
    global $db;
    if (isset($_GET[$val])) {
        return $_GET[$val];
    } else if (isset($_SESSION["calcul"][$val])) {
        return $_SESSION["calcul"][$val];
    } else if (isset($db[$val])) {
        return $db[$val];
    }
    return "";
}

function displayError(string $name, array $errors): void {
    if (isset($errors[$name])) {
        foreach ($errors[$name] as $key => $error) {
            echo "<p class=\"err\">$error</p>";
        }
    }
}

function validate(array $inputs): array {
    $errors = [];
    foreach ($inputs as $inputName => $inputParams) {
        if (in_array("required", $inputParams) && (!(isset($_GET[$inputName])) || $_GET[$inputName] == "" )) {
            $errors[$inputName][] = "ce champs est requis";
        }else {
            if (in_array("num", $inputParams) && !is_numeric($_GET[$inputName])) {
                $errors[$inputName][] = "ce champs n'est pas numérique";
            }
        }
    }
    return $errors;
}