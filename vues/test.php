<?php
require_once("../include/fct.inc.php");
require_once ("../include/class.pdogsb.inc.php");
$pdo = PdoGsb::getPdoGsb();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
$variable = "ma variable en list";

$date = date("m.d.Y");
var_dump($date);

list($jour,$mois,$annee) = explode(".",$date);

var_dump($annee);

@list($ma,$var,$en,$list) = explode(" ", $variable);

var_dump($var);

// Affiche : July 1, 2000 est un Saturday
echo "July 1, 2000 est un " . date("l", mktime(0, 0, 0, 7, 1, 2000));

$mois = getMois(date("d/m/Y"));
var_dump($mois);
$numAnnee =substr( $mois,0,4);
var_dump($numAnnee);
$numMois =substr( $mois,4,2);
var_dump($numMois);
$numMois -= 1;
var_dump($numMois);

$visiteur ="a17";
$dernierMois = $pdo->dernierMoisSaisi($visiteur);
var_dump($dernierMois);


$mois = "201512";
$moisSuivant = getMoisSuivant($mois);
var_dump($moisSuivant);
*/

$visiteur = "a17";
$moisSuivant = "201610";
$leLibelleConcerne = "location salle";
$dateActuelle = "23/10/2016";
$leMontantConcerne = 500;
$pdo->creeNouveauFraisHorsForfait($visiteur,$moisSuivant,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);