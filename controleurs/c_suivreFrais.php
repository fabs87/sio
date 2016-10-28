<?php
include("vues/v_sommaireComptable.php");
$action = $_REQUEST['action'];
switch ($action) {
    case 'selectionFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeFrais.php");
            break;
        }
    case 'voirEtatFrais': {
            $leMois = $_REQUEST['lstMois'];
            $idVisiteur = $_REQUEST['lstVisiteur'];
            $lesFrais = $pdo->getFicheAValider($idVisiteur, $leMois);
            if (empty($lesFrais)) {
                ajouterErreur("Pas de frais à Valider pour ce visiteur");
                include("vues/v_erreurs.php");
            } else {
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
            $leMois = $_REQUEST['mois'];
            $idVisiteur = $_REQUEST['visiteur'];
            $etat = "RB";
            $pdo->validationMois($idVisiteur, $leMois, $etat);
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