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
            $visiteur = $_REQUEST['lstVisiteur'];
            $leMois = $_REQUEST['lstMois'];
            $_SESSION['leMois'] = $leMois;
            $numAnnee = substr($leMois, 0, 4);
            $_SESSION['numAnnee'] = $numAnnee;
            $numMois = substr($leMois, 4, 2);
            $_SESSION['numMois'] = $numMois;
            $_SESSION['leVisiteur'] = $visiteur;
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $leMois);
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
            $visiteur = $_SESSION['leVisiteur'];
            $leMois = $_SESSION['leMois'];
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($visiteur, $leMois, $lesFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $leMois);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $leMois);
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
            $visiteur = $_SESSION['leVisiteur'];
            $leMois = $_SESSION['leMois'];
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            $pdo->majLibelleRefuse($idFrais);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $leMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $leMois);
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
            $visiteur = $_SESSION['leVisiteur'];
            $leMoisSelectionne = $_SESSION['leMois'];
            $moisSuivant = getMoisSuivant($leMoisSelectionne);
            $numMois = $_SESSION['numMois'];
            $numAnnee = $_SESSION['numAnnee'];
            $leFrais = $pdo->getUnFraisHorsForfait($idFrais);
            $leLibelleConcerne = $leFrais['libelle'];
            $leMontantConcerne = $leFrais['montant'];
            $dateActuelle = date("d/m/Y");
            if (!$pdo ->estPremierFraisMois($visiteur, $moisSuivant)){
                $pdo->creeNouveauFraisHorsForfait($visiteur,$moisSuivant,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $leMoisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $leMoisSelectionne);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            }else{
                $pdo->creeNouvellesLignesFrais($visiteur,$moisSuivant);
                $pdo->creeNouveauFraisHorsForfait($visiteur,$moisSuivant,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $leMoisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $leMoisSelectionne);
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
            $visiteur = $_SESSION['leVisiteur'];
            $mois = $_REQUEST['mois'];
            $etat = "CL";
            $pdo->majEtatFicheFrais($visiteur,$mois, $etat);
            break;
    }   

}
?>