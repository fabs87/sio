<?php
require_once("include/fct.inc.php");
require_once ("include/class.pdogsb.inc.php");
$pdo = PdoGsb::getPdoGsb();
/*
$visiteur = "a17";
$mois = "201607";
$test = $pdo->comptabiliserLesFraisHorsForfait($visiteur,$mois);
var_dump($test);

$chaine = "titotot.'oo/tio";
$test= filtrerChainePourBD($chaine);
var_dump($test);
 */
$id ="f1";
 $crypt = $pdo->hashDuMotDePasse($id);