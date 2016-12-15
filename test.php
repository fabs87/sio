<?php
require_once("include/fct.inc.php");
require_once ("include/class.pdogsb.inc.php");
$pdo = PdoGsb::getPdoGsb();

$frais = $pdo->getLesFraisForfait('a17','201608');
var_dump($frais);
$montant = 0;
foreach($frais as $unFrais){
    var_dump($unFrais);
    if ($unFrais['idfrais'] == "ETP"){
        $montant += $montant + ($unFrais['quantite'] * 80);
    }
  //      if ($unFrais['idfrais'] == "KM"){
  //      $montant += $montant + ($unFrais['quantite'] * 0.62);
    //     var_dump($montant);
    //}
        if ($unFrais['idfrais'] == "REP"){
        $montant += $montant + ($unFrais['quantite'] * 29);
         var_dump($montant);
    }
    var_dump($montant);
}

$montant = $pdo->comptabiliserLesFraisHorsForfait('a17','201608');
var_dump($montant);