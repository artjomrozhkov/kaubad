<?php
require("abifunktsioonid.php");

$sorttulp = "nimetus";
$otsisona = "";

if (isset($_REQUEST["sorttulp"])) {
    $sorttulp = $_REQUEST["sorttulp"];
}
if (isset($_REQUEST["otsisona"])) {
    $otsisona = $_REQUEST["otsisona"];
}

if (isset($_REQUEST["grupilisamine"]) && !empty($_REQUEST["uuegrupinimi"])) {
    global $yhendus;
    $kaubagrupp = $_REQUEST["uuegrupinimi"];
    $query = mysqli_query($yhendus,"SELECT * FROM kaubagrupid WHERE grupinimi='$kaubagrupp'");
    if (!empty(trim($_REQUEST["uuegrupinimi"])) && mysqli_num_rows($query) == 0) {
        lisaGrupp($_REQUEST["uuegrupinimi"]);
        header("Location: kaubahaldus.php");
        exit();
    } else {
         $error="Kaubagrupinimi on olemas";
    }
    $error="Kaubagrupp ei pea olama tühi";
}

if (isset($_REQUEST["tootjalisamine"]) && !empty($_REQUEST["uuetootjanimi"])) {
    global $yhendus;
    $tootjagrupp = $_REQUEST["uuetootjanimi"];
    $query = mysqli_query($yhendus,"SELECT * FROM tootja WHERE tootjanimi='$tootjagrupp'");
    if (!empty(trim($_REQUEST["uuetootjanimi"])) && mysqli_num_rows($query) == 0) {
        lisaTootja($_REQUEST["uuetootjanimi"]);
        header("Location: kaubahaldus.php");
        exit();
    } else {
        $error="Tootjagrupinimi on olemas";
    }
    $error="Tootjagrupp ei pea olama tühi";
}

if (isset($_REQUEST["kaubalisamine"]) && !empty($_REQUEST['nimetus'] && !empty($_REQUEST['hind']))) {
    lisaKaup($_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"],$_REQUEST["tootjaID"], $_REQUEST["hind"]);
    header("Location: kaubahaldus.php");
    exit();
}
if (isset($_REQUEST["kustutusid"])) {
    kustutaKaup($_REQUEST["kustutusid"]);
}
if (isset($_REQUEST["muutmine"])) {
    muudaKaup($_REQUEST["muudetudid"], $_REQUEST["nimetus"],
        $_REQUEST["kaubagrupi_id"],$_REQUEST["tootjaID"], $_REQUEST["hind"]);
}
$kaubad = kysiKaupadeAndmed($sorttulp, $otsisona);
?>
<!DOCTYPE html>"
<html>
<head>
    <header>
        <h1>Kaupade holdus</h1>
    </header>
    <title>Kaupade leht</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <style>
        table {
            border: 1px solid black;
        }

        TD, TH {
            padding: 16px;
            border: 1px solid black;
        }

        TH {
            background: #b0e0e6;
        }

        div#menu {
            padding: 10px;
            margin-top: 1%;
            float: left;
            background-color: #b0e0e6;
            text-decoration: none;
            border: 1px solid black;
        }

        div#sisu {
            padding: 10px;
            float: left;
            margin-top: 1%;
            margin-left: 1%;
            background-color: #b0e0e6;
            border: 1px solid black;

        }

        a {
            display: inline-block;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 5px 1px;
            padding: 5px 1px;
            font-size: 17px;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            transition: 0.4s ease-in-out;
        }
    </style>
</head>
<body>
<form action="kaubahaldus.php">
    <h2>Kaupade loetelu</h2>
    <div class="container">
        <form action="kaubahaldus.php">
            <input type="text" name="otsisona" placeholder="Otsi...">
        </form>
    </div>
    <table>
        <tr>
            <th>Haldus</th>
            <th><a href="kaubahaldus.php?sorttulp=nimetus">Nimetus</a></th>
            <th><a href="kaubahaldus.php?sorttulp=grupinimi">Kaubagrupp</a></th>
            <th><a href="kaubahaldus.php?sorttulp=hind">Hind</a></th>
            <th><a href="kaubahaldus.php?sorttulp=tootjanimi">Tootja</a></th>
        </tr>
        <?php foreach ($kaubad as $kaup): ?>
            <tr>
                <?php if (isset($_REQUEST["muutmisid"]) &&
                    intval($_REQUEST["muutmisid"]) == $kaup->id): ?>
                    <td>
                        <input type="submit" name="muutmine" value="Muuda"/>
                        <input type="submit" name="katkestus" value="Katkesta"/>
                        <input type="hidden" name="muudetudid" value="<?= $kaup->id ?>"/>
                    </td>
                    <td><input type="text" name="nimetus" value="<?= $kaup->nimetus ?>"/></td>
                    <td><?php
                        echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid",
                            "kaubagrupi_id", $kaup->kaubagrupi_id);
                        ?></td>
                    <td><input type="text" name="hind" value="<?=$kaup->hind ?>"/></td>
                    <td><?php
                        echo looRippMenyy("SELECT id, tootjanimi FROM tootja",
                            "kaubagrupi_id", $kaup->tootjaID);
                        ?></td>
                <?php else: ?>

                    <td><a href="kaubahaldus.php?kustutusid=<?=$kaup->id ?>"
                           onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                        <a href="kaubahaldus.php?muutmisid=<?=$kaup->id ?>">m</a>
                    </td>
                    <td><?= $kaup->nimetus ?></td>
                    <td><?= $kaup->grupinimi ?></td>
                    <td><?= $kaup->hind ?></td>
                    <td><?= $kaup->tootjanimi ?></td>
                <?php endif ?>
            </tr>
        <?php endforeach; ?>
    </table>
</form>
<form action="kaubahaldus.php">
    <div id="menu">
        <h2>Kauba lisamine</h2>
        <h3>Nimetus:</h3>
        <input type="text" name="nimetus"/>
        <h3>Kaubagrupp:</h3>
        <?php
        echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid",
            "kaubagrupi_id");
        ?>
        <h3>Tootja:</h3>
        <?php
        echo looRippMenyy("SELECT id, tootjanimi FROM tootja",
            "tootjaID");
        ?>
        <h3>Hind:</h3>
        <input type="text" name="hind"/>
        <input type="submit" name="kaubalisamine" value="Lisa kaup"/>
    </div>
    <div id="sisu">
        <h2>Grupi lisamine</h2>
        <input type="text" name="uuegrupinimi" pattern="[A-Za-z].{3,}"/>
        <input type="submit" name="grupilisamine" value="Lisa grupp"/>
        <?php echo "<br><div style='color: red'>".($error ?? ""). "</div>"; ?>

        <h2>Tootja lisamine</h2>
        <input type="text" name="uuetootjanimi" pattern="[A-Za-z].{3,}"/>
        <input type="submit" name="tootjalisamine" value="Lisa tootja"/>
        <?php echo "<br><div style='color: red'>".($error ?? ""). "</div>"; ?>
    </div>
</form>
</body>
</html>

