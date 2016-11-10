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
   
            $leMois = $_REQUEST['lstMois'];
            $idVisiteur = $_REQUEST['lstVisiteur'];
            $lesFrais = $pdo->getFicheAValider($idVisiteur, $leMois);
            if (empty($lesFrais)) {
                ajouterErreur("Pas de frais à Valider ou Mise en paiement pour ce visiteur et pour ce mois");
                include("vues/v_erreurs.php");
            }else{
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
                $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
                $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
                $numAnnee = substr($leMois, 0, 4);
                $numMois = substr($leMois, 4, 2);
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
            $leMois = $_REQUEST['hdmois'];
            $idVisiteur = $_REQUEST['hdvisiteur'];
            $etat = "MP";
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
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
    case 'rembourse':{
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeSuiviFrais.php");
            $leMois = $_REQUEST['hdmois'];
            $idVisiteur = $_REQUEST['hdvisiteur'];
            $etat = "RB";
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
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