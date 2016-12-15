<?php

include("../include/class.pdogsbForTest.php");
include("../include/fct.inc.php");

class PdoGsbTest extends PHPUnit_Extensions_Database_TestCase {

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;
    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection() {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    public function getDataSet() {
        return $this->createMySQLXMLDataSet('xmlFixtureFast.xml');
    }

    public function testGetNombreEtat(){
    $this->assertEquals(5, $this->getConnection()->getRowCount('etat'));
    }
    
  
    public function testGetLesInfosFicheFrais() {
       $query = $this->getConnection()->createQueryTable("fichefrais", "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais join Etat on fichefrais.idEtat = Etat.id where fichefrais.idvisiteur='a131' and fichefrais.mois='201609'");
       $resultatAttendu = $this->createXmlDataset('infoFraisOk.xml')->getTable('fichefrais');
       $this->assertTablesEqual($resultatAttendu, $query);
    }

    public function testComptabiliserLesFraisHorsForfait() {
        $visiteur = "a131";
        $mois = "201609";
        $pdo = PdoGsb::getPdoGsb();
        $res = $pdo->comptabiliserLesFraisHorsForfait($visiteur, $mois);
        $this->assertEquals(1386.00, $res);
    }

    public function testGetFicheAValider() {
        $query1 = $this->getConnection()->createQueryTable("fichefrais", "select * from  fichefrais where fichefrais.idvisiteur='a131' and fichefrais.idetat='VA'");
        $resultatAttendu = $this->createXmlDataset('ficheAValider.xml')->getTable('fichefrais');
        $this->assertTablesEqual($resultatAttendu, $query1);
    }
    
    public function testMajLibelleRefuse() {  
        $idFrais = 261;
        $pdo = PdoGsb::getPdoGsb();
        $test = $pdo->majLibelleRefuse($idFrais);
        $query = $this->getConnection()->createQueryTable("lignefraishorsforfait", "select libelle as libelle from lignefraishorsforfait where id = '$idFrais'");
        $resultatAttendu = $this->createXmlDataset('libelleRefuse.xml')->getTable("lignefraishorsforfait");
        $this->assertTablesEqual($resultatAttendu, $query);
    }   
    
    public function testSupprimerFraisHorsForfait(){
        $idFrais = 237;   
        $pdo =  PdoGsb::getPdoGsb();
        $suppression = $pdo->SupprimerFraisHorsForfait($idFrais);
        $this->assertEquals(29, $this->getConnection()->getRowCount('lignefraishorsforfait'));        
     }
     
     
    public function testGetInfosVisiteur() {
        $query = $this->getConnection()->createQueryTable("visiteur", "select login as login, mdp as mdp from visiteur where login='lvillachane'");
        $resultatAttendu = $this->createXmlDataset('infoVisiteurOk.xml')->getTable("visiteur");
        $this->assertTablesEqual($resultatAttendu,$query);
    }
    
    
     
    public function testMajNbJustificatifs() {
        $idVisiteur = "a131";
        $mois = "201609";
        $nbJustificatifs = 37;
        
        $pdo = PdoGsb::getPdoGsb();
        $test = $pdo->MajNbJustificatifs($idVisiteur,$mois,$nbJustificatifs);
        $query = $this->getConnection()->createQueryTable("fichefrais", "select nbJustificatifs as nbJustificatifs from fichefrais where idVisiteur = 'a131' and mois='201609'");
        $resultatAttendu = $this->createXmlDataset('nbJustificatif.xml')->getTable("fichefrais");
        $this->assertTablesEqual($resultatAttendu, $query);
        
    }
    
     /* 
    public function testCreeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
     */

    
    }
