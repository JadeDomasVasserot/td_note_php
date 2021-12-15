<?php
    session_start();
    $db = [];
    require_once "functions.php";
    // login in DB
    try {
    $pdo = new PDO ("mysql:host=localhost;dbname=figure", "root", "");
    }
    catch (Exception $e) { // permet de capturer les erreurs.
        die ("Une erreur est survenue. $e"); // arrêter scripts et afficher valeurs
    }

    if (!isset($_SESSION["calcul"]) || count($_SESSION["calcul"]) <= 0) {
        $getLatestCalc = $pdo->prepare("SELECT id, trapeze.*, losange.*,
        losange.aire as losangeR,
        trapeze.aire as trapezeR
        FROM calcul
        LEFT JOIN trapeze ON trapeze.idcalcul = calcul.id
        LEFT JOIN losange ON losange.idcalcul = calcul.id
        ORDER BY calcul.id DESC LIMIT 1");
        $getLatestCalc->execute();
        
        $db = $getLatestCalc->fetchAll()[0];
    }
    $errors = [];
    $result = null;
    if(isset($_GET["shape"]) && array_key_exists($_GET["shape"], $shapes)) {
        if ($_GET["shape"] == "trapeze") {
            $errors = validate([
                "sB" => ["required", "num"],
                "b" => ["required", "num"],
                "h" => ["required", "num"]
            ]);
            // check if there are not errors in the form
            if (count($errors) <= 0) {
                $result = $shapes["trapeze"]($_GET["sB"], $_GET["b"], $_GET["h"]);
                // put calcul in session
                if (isset($_GET["calcul"])) {
                    unset($_SESSION["calcul"]);
                    // in sesion
                    $_SESSION["calcul"] = [
                        "shape" => "trapeze",
                        "sB" => $_GET["sB"],
                        "b" => $_GET["b"],
                        "h" => $_GET["h"],
                        "result" => $result
                    ];
                // put calcul in DB
                } else if(isset($_GET["enregistrer"])) {
                    $createCalcul = $pdo->prepare("START TRANSACTION;
                        INSERT INTO calcul() VALUES();
                        INSERT INTO trapeze (idcalcul, sB, b, h, aire)
                        VALUES (LAST_INSERT_ID(), :sB, :b, :h, :aire);
                        COMMIT;");
                    $createCalcul->execute([
                        "sB" => $_GET["sB"],
                        "b" => $_GET["b"],
                        "h" => $_GET["h"],
                        "aire" => $result,
                    ]);
                }
            }
        // same but for losange
        } else {
            $errors = validate([
                "pD" => ["required", "num"],
                "gD" => ["required", "num"],
            ]);
            if (count($errors) <= 0) {
                $result = $shapes["losange"]($_GET["pD"], $_GET["gD"]);
                if (isset($_GET["calcul"])) {
                    unset($_SESSION["calcul"]);
                    // in session
                    $_SESSION["calcul"] = [
                        "shape" => "losange",
                        "pD" => $_GET["pD"],
                        "gD" => $_GET["gD"],
                        "result" => $result
                    ];
                } else if(isset($_GET["enregistrer"])) {
                    $createCalcul = $pdo->prepare("START TRANSACTION;
                        INSERT INTO calcul() VALUES();
                        INSERT INTO losange (idcalcul, pD, gD, aire)
                        VALUES (LAST_INSERT_ID(), :pD, :gD, :aire);
                        COMMIT;");
                    $createCalcul->execute([
                        "pD" => $_GET["pD"],
                        "gD" => $_GET["gD"],
                        "aire" => $result,
                    ]);
                }
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css"/>
    <title>Document</title>
</head>
<body>
    <main>
        <form action="." method="GET">
                    <!-- Choix de la figure !-->
            <label>formes :</label>
            <select name="shape" id="shape">
                <?php
                    foreach ($shapes as $shapeName => $value) {
                        ?>
                            <option <?php
                                if(getData("shape") == $shapeName || (getData("shape") != $shapeName && isset($db[$shapeName."R"]))) {
                                    echo "selected";
                                }
                            ?> value="<?php echo $shapeName ?>"><?php echo $shapeName ?></option>
                        <?php
                    }
                ?>
            </select>
            <!-- Rentrer les valeurs !-->
            <!-- Trapèze !-->
            <div id="trap">
                <div clas="text">
                    <label for="sB"> Petite base : </label>
                    <input id="sB" type="number" name="sB" value="<?php echo getData("sB"); ?>">
                    <?php displayError("sB", $errors); ?>
                </div>
                <div class="text">
                    <label for="b"> Grande base : </label>
                    <input id="b" type="number" name="b" value="<?php echo getData("b"); ?>">
                    <?php displayError("b", $errors); ?>
                </div>
                <div class="text">
                    <label for="h"> Hauteur : </label>
                    <input id="h" type="number" name="h" value="<?php echo getData("h"); ?>">
                    <?php displayError("h", $errors); ?>
                </div>
            </div>
            <!-- Losange !-->
            <div id="los">
                 <div class="text">
                    <label for="pD">Petite diagonale :</label>
                    <input id = "pD "type="number" name="pD" value="<?php echo getData("pD"); ?>">
                    <?php displayError("pD", $errors); ?>
                </div>
                <div class="text">
                    <label for="gD">  Grande diagonale :</label>
                    <input id = "gD" type="number" name="gD" value="<?php echo getData("gD"); ?>">
                    <?php displayError("gD", $errors); ?>
                </div>
            </div>
            <button type="submit" name="calcul">Calculer</button>
            <button type="submit" name="enregistrer"> Enregistrer</button>
        </form>
        <br/>
        <?php
            if (isset($result)) {
                ?>
                    <strong>l'aire du <?php echo getData("shape") ?> est de <?php echo $result ?></strong>
                <?php
            } else if (getData("result") != "") {
                ?>
                    <strong>l'aire du <?php echo getData("shape") ?> est de <?php echo getData("result") ?></strong>
                <?php
            } else if (count($db) > 0) {
                if (isset($db["sB"])) {
                    ?>
                        <strong>l'aire du trapeze est de <?php echo $db["trapezeR"] ?></strong>
                    <?php
                } else if (isset($db["pD"])) {
                    ?>
                        <strong>l'aire du losange est de <?php echo $db["losangeR"] ?></strong>
                    <?php
                }
            }
        ?>
    </main>
    <script>
        if(document.getElementById("shape").value == "trapeze") {
            document.getElementById("los").style.display = "none";
        } else {
            document.getElementById("trap").style.display = "none";
        }
        document.getElementById("shape").onchange = () => {
            if (document.getElementById("shape").value == "trapeze") {
                document.getElementById("los").style.display = "none";
                document.getElementById("trap").style.display = "block";
            } else if (document.getElementById("shape").value == "losange") {
                document.getElementById("los").style.display = "block";
                document.getElementById("trap").style.display = "none";
            }
        }
    </script>
</body>
</html>