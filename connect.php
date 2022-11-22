<?php
$kasutaja='artjomrozhkov'; // d113371_artjom
$server='localhost'; // d113371.mysql.zonevs.eu
$andmebaas='artjomrozhkov'; // d113371_baas
$salasyna='1234'; // syRWwjXQJR2lglYGDMk6

$yhendus= new mysqli($server,$kasutaja,$salasyna,$andmebaas);
$yhendus->set_charset('UTF8');