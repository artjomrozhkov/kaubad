<?php
require_once("connect.php");
$sorttulp="nimetus";
$otsisona="";

if(isset($_REQUEST["sorttulp"])){
    $sorttulp=$_REQUEST["sorttulp"];
}
//andmete sorteerimine
function kysiKaupadeAndmed($sorttulp="nimetus", $otsisona=""){
    global $yhendus;
    $lubatudtulbad=array("nimetus", "grupinimi", "hind","tootjanimi");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }
    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT kaubad.id, nimetus, grupinimi, kaubagrupi_id, tootjanimi, tootjaID, hind
       FROM kaubad, kaubagrupid, tootja
       WHERE kaubad.kaubagrupi_id=kaubagrupid.id AND kaubad.tootjaID=tootja.id
        AND (nimetus LIKE '%$otsisona%' OR grupinimi LIKE '%$otsisona%' OR tootjanimi LIKE '%$otsisona%')
       ORDER BY $sorttulp");
    $kask->bind_result($id, $nimetus, $grupinimi, $kaubagrupi_id,$tootjanimi,$tootjaID, $hind);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->nimetus=htmlspecialchars($nimetus);
        $kaup->grupinimi=htmlspecialchars($grupinimi);
        $kaup->kaubagrupi_id=$kaubagrupi_id;
        $kaup->hind=$hind;
        $kaup->tootjanimi=htmlspecialchars($tootjanimi);
        $kaup->tootjaID=$tootjaID;
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}

/**
 * Luuakse HTML select-valik, kus v6etakse v22rtuseks sqllausest tulnud
 * esimene tulp ning n2idatakse teise tulba oma.
 */
//dropdown list tabelist kaubagrupid grupinimi
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}

//lisamine grupinimi
function lisaGrupp($grupinimi){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO kaubagrupid (grupinimi)
                      VALUES (?)");
    $kask->bind_param("s", $grupinimi);
    $kask->execute();
}

function lisaTootja($tootjanimi){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO tootja (tootjanimi)
                      VALUES (?)");
    $kask->bind_param("s", $tootjanimi);
    $kask->execute();
}

//lisamine nimetus ja hind on tabel
function lisaKaup($nimetus, $kaubagrupi_id,$tootjaID, $hind){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO 
       kaubad (nimetus, kaubagrupi_id,tootjaID, hind)
       VALUES (?, ?, ?, ?)");
    $kask->bind_param("siid", $nimetus, $kaubagrupi_id,$tootjaID, $hind);
    $kask->execute();
}
//kustutamine kauba_id on tabel
function kustutaKaup($kauba_id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM kaubad WHERE id=?");
    $kask->bind_param("i", $kauba_id);
    $kask->execute();
}

function muudaKaup($kauba_id, $nimetus, $kaubagrupi_id,$tootjaID, $hind){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE kaubad SET nimetus=?, kaubagrupi_id=?,tootjaID=?, hind=?
                      WHERE id=?");
    $kask->bind_param("sidi", $nimetus, $kaubagrupi_id,$tootjaID, $hind, $kauba_id);
    $kask->execute();
}

//---------------
//if(array_pop(explode("/", $_SERVER["PHP_SELF"]))=="abifunktsioonid.php"):
?>
    <pre>
</pre>
