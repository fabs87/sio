<?php

include("vues/v_sommaireComptable.php");
$action = $_REQUEST['action'];
switch ($action) {
    case 'selectionMoisVisiteur': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            break;
        }
    case 'voirLesFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            $leVisiteur = $_REQUEST['lstVisiteur'];
            $leMois = $_REQUEST['lstMois'];
            memoriseLeVisiteur($leMois, $leVisiteur);
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
            if (empty($lesFraisForfait) && empty($lesFraisHorsForfait)) {
                ajouterErreur("Pas de fiche de Frais pour ce mois");
                include("vues/v_erreurs.php");
            }else{
            include("vues/v_listeFraisForfaitAValider.php");
            include("vues/v_listeFraisHorsForfaitAValider.php");
            include("vues/v_validationFrais.php");
            }
            break;
        }
    case 'validerMajFraisForfait': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            $lesFrais = $_REQUEST['lesFrais'];
            $leVisiteur = $_SESSION['leVisiteur'];
            $leMois = $_SESSION['leMois'];
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($leVisiteur, $leMois, $lesFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
                $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            } else {
                ajouterErreur("Les valeurs des frais doivent être numériques");
                include("vues/v_erreurs.php");
            }
            break;
        }
    case 'refuserFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            $idFrais = $_REQUEST['idFrais'];
            $leVisiteur = $_SESSION['leVisiteur'];
            $leMois = $_SESSION['leMois'];
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            $pdo->majLibelleRefuse($idFrais);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
            include("vues/v_listeFraisForfaitAValider.php");
            include("vues/v_listeFraisHorsForfaitAValider.php");
            include("vues/v_validationFrais.php");
            break;
        }
    case 'reporterFrais': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            $idFrais = $_REQUEST['idFrais'];
            $leVisiteur = $_SESSION['leVisiteur'];
            $leMoisSelectionne = $_SESSION['leMois'];
            $moisSuivant = getMoisSuivant($leMoisSelectionne);
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            $leFrais = $pdo->getUnFraisHorsForfait($idFrais);
            $leLibelleConcerne = $leFrais['libelle'];
            $leMontantConcerne = $leFrais['montant'];
            $dateActuelle = date("d/m/Y");
            if (!$pdo ->estPremierFraisMois($leVisiteur, $moisSuivant)){
                $pdo->creeNouveauFraisHorsForfait($leVisiteur,$moisSuivant,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMoisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMoisSelectionne);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            }else{
                $pdo->creeNouvellesLignesFrais($leVisiteur,$moisSuivant);
                $pdo->creeNouveauFraisHorsForfait($leVisiteur,$moisSuivant,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMoisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMoisSelectionne);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            }
            break;
        }
    case 'validationFrais':{
            $lesVisiteurs = $pdo->getListeVisiteur();
            $lesMois = $pdo->getListeMois();
            include("vues/v_listeMoisEtVisiteur.php");
            $leVisiteur = $_SESSION['leVisiteur'];
            $mois = $_REQUEST['mois'];
            $etat = "CL";
            $pdo->majEtatFicheFrais($leVisiteur,$mois, $etat);
            break;
    }   

}
?>