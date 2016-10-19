<?php

include("vues/v_sommaireComptable.php");
$moisPrecedent = getMoisPrecedent(date("d/m/Y"));
$numAnnee = substr($moisPrecedent, 0, 4);
$numMois = substr($moisPrecedent, 4, 2);
$action = $_REQUEST['action'];
switch ($action) {
    case 'selectionMoisVisiteur': {
            $lesVisiteurs = $pdo->getListeVisiteur();
            include("vues/v_listeFraisVisiteurParMois.php");
            
            break;
        }
    case 'voirLesFrais': {
            //$mois = $_REQUEST['lstMois'];
            $visiteur = $_REQUEST['lstVisiteur'];
            $_SESSION['leVisiteur'] = $visiteur;
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $moisPrecedent);
            $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $moisPrecedent);
            if (!array($lesFraisForfait) && !array($lesFraisHorsForfait)) {
                ajouterErreur("Pas de fiche de Frais pour ce mois");
                include("vues/v_erreurs.php");
            }
            include("vues/v_listeFraisForfaitAValider.php");
            include("vues/v_listeFraisHorsForfaitAValider.php");
            include("vues/v_validationFrais.php");
            break;
        }
    case 'validerMajFraisForfait': {
            $lesFrais = $_REQUEST['lesFrais'];
            $visiteur = $_SESSION['leVisiteur'];
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($visiteur, $moisPrecedent, $lesFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $moisPrecedent);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $moisPrecedent);
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
            $idFrais = $_REQUEST['idFrais'];
            $visiteur = $_SESSION['leVisiteur'];
            $pdo->majLibelleRefuse($idFrais);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $moisPrecedent);
            $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $moisPrecedent);
            include("vues/v_listeFraisForfaitAValider.php");
            include("vues/v_listeFraisHorsForfaitAValider.php");
            include("vues/v_validationFrais.php");
            break;
        }
    case 'reporterFrais': {
            $idFrais = $_REQUEST['idFrais'];
            $visiteur = $_SESSION['leVisiteur'];
            $leFrais = $pdo->getUnFraisHorsForfait($idFrais);
            $leLibelleConcerne = $leFrais['libelle'];
            $leMontantConcerne = $leFrais['montant'];
            $moisActuel = getMois(date("d/m/Y"));
            $dateActuelle = date("d/m/Y");

            if ($dernierMois = $pdo->dernierMoisSaisi($visiteur) == $moisActuel){
                $pdo->creeNouveauFraisHorsForfait($visiteur,$moisActuel,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $moisPrecedent);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $moisPrecedent);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            }else{
                $pdo->creeNouvellesLignesFrais($visiteur,$moisActuel);
                $pdo->creeNouveauFraisHorsForfait($visiteur,$moisActuel,$leLibelleConcerne,$dateActuelle,$leMontantConcerne);
                $pdo->supprimerFraisHorsForfait($idFrais);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($visiteur, $moisPrecedent);
                $lesFraisForfait = $pdo->getLesFraisForfait($visiteur, $moisPrecedent);
                include("vues/v_listeFraisForfaitAValider.php");
                include("vues/v_listeFraisHorsForfaitAValider.php");
                include("vues/v_validationFrais.php");
            }
            break;
        }
    case 'validationFrais':{
            $visiteur = $_SESSION['leVisiteur'];
            $mois = $_REQUEST['mois'];
            $pdo->validationMois($visiteur,$mois);
    }        
}
?>