﻿<?php

/**
 * Classe d'accès aux données. 

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsbunit';
    private static $user = 'unit';
    private static $mdp = 'unit';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    public function __construct() {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur et son profil afin de
     * différencier le profil visiteur et le profil comptable 
     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom et le profil sous la forme d'un tableau associatif 
     */
    public function getInfosVisiteur($login, $mdp) {
        $req1 = "select visiteur.mdp as mdp from visiteur where visiteur.login = '".filtrerChainePourBD($login)."'";
        $res1 = PdoGsb::$monPdo->query($req1);
        $liste = $res1->fetch();
        if (password_verify($mdp, $liste['mdp'])){
            $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.profil as profil from visiteur 
		where visiteur.login='".filtrerChainePourBD($login)."'";
            $rs = PdoGsb::$monPdo->query($req);
            $ligne = $rs->fetch();
        }
        return $ligne;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' 
		and lignefraishorsforfait.mois = '".filtrerChainePourBD($mois)."'";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs 
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' and fichefrais.mois = '".filtrerChainePourBD($mois)."'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' and lignefraisforfait.mois='".filtrerChainePourBD($mois)."' 
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait
     * @return un tableau associatif 
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif 
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '".filtrerChainePourBD($idVisiteur)."' and lignefraisforfait.mois = '".filtrerChainePourBD($mois)."'
			and lignefraisforfait.idfraisforfait = '".filtrerChainePourBD($unIdFrais)."'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '".filtrerChainePourBD($idVisiteur)."' and fichefrais.mois = '".filtrerChainePourBD($mois)."'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux 
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '".filtrerChainePourBD($idVisiteur)."'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur 
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '".filtrerChainePourBD($idVisiteur)."'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('".filtrerChainePourBD($idVisiteur)."','".filtrerChainePourBD($mois)."',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('".filtrerChainePourBD($idVisiteur)."','".filtrerChainePourBD($mois)."','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj/mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait values(NULL,'".filtrerChainePourBD($idVisiteur)."','".filtrerChainePourBD($mois)."','".filtrerChainePourBD($libelle)."','$dateFr','".filtrerChainePourBD($montant)."')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Récupération d'un frais hors forfait afin de le mémoriser avant le report
     * nouveau
     * @param type $idfrais
     * @return $leFraisHorsForfait
     */
    public function getUnFraisHorsForfait($idfrais) {
        $req = "select lignefraishorsforfait.idVisiteur as idvisiteur, lignefraishorsforfait.mois "
                . "as mois, lignefraishorsforfait.libelle as libelle, lignefraishorsforfait.montant"
                . " as montant from lignefraishorsforfait where lignefraishorsforfait.id = $idfrais";
        $res = PdoGsb::$monPdo->query($req);
        $leFraisHorsForfait = $res->fetch();
        return $leFraisHorsForfait;
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais 
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id ='".filtrerChainePourBD($idFrais)."'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Ajout de la mentien REFUSE dans le libellé d'un frais non valide
     * Vérification si le mot REFUSE est déja présent, cela signifie que
     * le REFUS a déja été fait donc aucun update à faire. Troncature de la chaine
     * afin qu'elle ne dépasse pas la taille.
     * nouveau
     * @param type $idFrais
     */
    public function majLibelleRefuse($idFrais) {
        $req = PdoGsb::$monPdo->prepare("select lignefraishorsforfait.libelle as libelle from lignefraishorsforfait where lignefraishorsforfait.id = '$idFrais'");
        $req->execute();
        $resultat = $req->fetch(PDO::FETCH_ASSOC);
        $libelle = $resultat['libelle'];
        if (!strstr($libelle, "REFUSE")) {
            $nouveauLibelle = "REFUSE $libelle";
            if (strlen($nouveauLibelle) >= 100) {
                $nouveauLibelle = substr($nouveauLibelle, 0, 99);
            }
            $requete = PdoGsb::$monPdo->prepare("update lignefraishorsforfait set lignefraishorsforfait.libelle = '$nouveauLibelle' where lignefraishorsforfait.id='$idFrais'");
            $requete->execute();
        }
    }

    /**
     * Requete pemettant d'aller chercher les douze derniers mois 
     * se trouvant dans la base.
     * nouveau
     * @return lesMois
     */
    public function getListeMois() {
        $req = "select distinct fichefrais.mois as mois from fichefrais order by fichefrais.mois desc limit 12";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = $res->fetchAll();
        return $lesMois;
    }

    /**
     *     
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' 
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs, 
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
			where fichefrais.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' and fichefrais.mois = '".filtrerChainePourBD($mois)."'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '".filtrerChainePourBD($etat)."', dateModif = now() 
		where fichefrais.idvisiteur ='".filtrerChainePourBD($idVisiteur)."' and fichefrais.mois = '".filtrerChainePourBD($mois)."'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Cherche dans la base la liste de tous les visiteurs qui ne sont pas 
     * comptable ( donc valeur du profil à 0) et le retourne dans un tableau
     * nouveau
     * @return $lesVisiteurs  
     */
    public function getListeVisiteur() {
        $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom FROM visiteur WHERE visiteur.profil = 0";
        $res = PdoGsb::$monPdo->query($req);
        $lesVisiteurs = $res->fetchAll();
        return $lesVisiteurs;
    }

    /**
     * Cherche dans la base les fiches ayant l'état à Valider et les retourne pour 
     * un visiteur et un mois donné
     * 
     * @return $laFiche
     */
    public function getFicheAValider($idVisiteur, $mois) {
        $req = PdoGsb::$monPdo->prepare("select idEtat as etat, mois as mois from fichefrais where fichefrais.idvisiteur = '".$idVisiteur."' and fichefrais.mois = '".$mois."' AND fichefrais.idetat = 'VA' OR fichefrais.idvisiteur = '".$idVisiteur."' and fichefrais.mois = '".$mois."' AND fichefrais.idEtat = 'MP'");
        $req->execute();
        $laFiche = $req->fetch(PDO::FETCH_ASSOC);
        return $laFiche;
    }

    /**
     * Comptablise le montant des frais hors forfait uniquement s'il n'y a pas de 
     * libelle REFUSE dans l'intitulé
     * 
     * @param  $idVisiteur
     * @param  $mois
     * @return float $montant
     */
    public function comptabiliserLesFraisHorsForfait($idVisiteur, $mois) {
        $montant = 0;
        $req = "select lignefraishorsforfait.libelle as libelle, lignefraishorsforfait.montant as montant "
                . "from lignefraishorsforfait where lignefraishorsforfait.idVisiteur = '$idVisiteur'"
                . "and lignefraishorsforfait.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $lesFraisHorsForfait = $res->fetchAll();

        //Vérification du libelle et ajout du montant s'il n'est pas refusé
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            if (!strstr($unFraisHorsForfait['libelle'], "REFUSE")) {
                $montant += $unFraisHorsForfait['montant'];
            }
        }
        return $montant;
    }

    /** 
     * Cryptage de l'ensemble des mots de passe de la base
     * @param  $idVisiteur
     */
    public function hashDesMotDePasse(){
        $req = "select visiteur.mdp as mdp, visiteur.id as id from visiteur";
        $res = PdoGsb::$monPdo->query($req);
        $lesMDP = $res->fetchAll();
        //Parcours des résultats hash des mots de passe et modification en base.
        foreach($lesMDP as $leMDP){
            $motDePasseCrypte = password_hash($leMDP['mdp'], PASSWORD_DEFAULT);
            $req1 ="update visiteur set visiteur.mdp='$motDePasseCrypte' where visiteur.id='".$leMDP['id']."'";
            PdoGsb::$monPdo->exec($req1);
        }
    }
    
     /** 
     * Cryptage du mot de passe concernant un visiteur donnée
     * 
     */
    public function hashDuMotDePasse($id){
        $req = "select visiteur.mdp as mdp, visiteur.id as id from visiteur WHERE visiteur.id = '$id'";
        $res = PdoGsb::$monPdo->query($req);
        $leMDP = $res->fetch();
        if (!empty($leMDP)){
            $motDePasseCrypte = password_hash($leMDP['mdp'], PASSWORD_DEFAULT);
            $req1 ="update visiteur set visiteur.mdp='$motDePasseCrypte' where visiteur.id='$id'";
            PdoGsb::$monPdo->exec($req1);
        }else{
            echo "Utilisateur inexistant";
        }
    }
}
?>