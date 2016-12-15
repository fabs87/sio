<?php
include("vues/v_sommaireComptable.php");
$action = $_REQUEST['action'];
switch ($action) {
    case 'selectionFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeSuiviFrais.php");
            break;
        }
    case 'voirEtatFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeSuiviFrais.php");
            $unMois = $_REQUEST['lstMois'];
            $unVisiteur = $_REQUEST['lstVisiteur'];
            $lesFrais = $pdo->getFicheAValider($unVisiteur, $unMois);
            if (empty($lesFrais)) {
                ajouterErreur("Pas de frais à Valider ou Mise en paiement pour ce visiteur et pour ce mois");
                include("vues/v_erreurs.php");
            }else{
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($unVisiteur, $unMois);
                $lesFraisForfait = $pdo->getLesFraisForfait($unVisiteur, $unMois);
                $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($unVisiteur, $unMois);
                $numAnnee = substr($unMois, 0, 4);
                $numMois = substr($unMois, 4, 2);
                $libEtat = $lesInfosFicheFrais['libEtat'];
                $montantValide = $lesInfosFicheFrais['montantValide'];
                $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
                $dateModif = $lesInfosFicheFrais['dateModif'];
                $dateModif = dateAnglaisVersFrancais($dateModif);
                include("vues/v_suiviFrais.php");
            }
            break;
        }
    case 'miseEnPaiement': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeSuiviFrais.php");
            $unMois = $_REQUEST['hdmois'];
            $unVisiteur = $_REQUEST['hdvisiteur'];
            $etat = "MP";
            $pdo->majEtatFicheFrais($unVisiteur, $unMois, $etat);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($unVisiteur, $unMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($unVisiteur, $unMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($unVisiteur, $unMois);
            $numAnnee = substr($unMois, 0, 4);
            $numMois = substr($unMois, 4, 2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_suiviFrais.php");
            break;
        }
    case 'rembourse':{
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeSuiviFrais.php");
            $unMois = $_REQUEST['hdmois'];
            $unVisiteur = $_REQUEST['hdvisiteur'];
            $etat = "RB";
            $pdo->majEtatFicheFrais($idVisiteur, $unMois, $etat);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($unVisiteur, $unMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($unVisiteur, $unMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($unVisiteur, $unMois);
            $numAnnee = substr($leMois, 0, 4);
            $numMois = substr($leMois, 4, 2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
            $dateModif = dateAnglaisVersFrancais($dateModif);
            include("vues/v_suiviFrais.php");
            break;
        }
}
?>