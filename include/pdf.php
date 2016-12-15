<?php
ob_end_clean();
require('fpdf.php');
include 'class.pdogsb.inc.php';
include 'fct.inc.php';
$id = $_REQUEST['id'];
$mois = $_REQUEST['mois'];
$pdo = PdoGsb::getPdoGsb();
/*Création d'un enregistrement dans la base afin d'adapter l'application
 * au GreenIT. Cela signifie nautoriser qu'une seule impression de la fiche
 */
$memorisationPdf = $pdo->pdfMemorisation($id, $mois);
if ($memorisationPdf == FALSE){
    echo PdoGsb::errorCode();
    echo mysql_errno();
    echo "Fiche déja imprimée";
}else{

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Image('../images/logo.jpg',10,6,30);
        $this->Ln(20);
        //Décalage à droite
        $this->Cell(80);
        //Titre
        $this->Cell(30, 10, 'REMBOURSEMENT DE FRAIS ENGAGES', 0, 1, 'C');
        $this->Line(0, 45, 450, 45);
        //Saut de ligne
        $this->Ln(20);
    }
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial', 'I', 8);
        // Numéro de page
        //$this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}
/*
 * Génération du PDF avec la mise en page souhaitée
 */
$pdf = new PDF();
$pdf->AddPage();
$fraisForfait = $pdo->getLesFraisForfaitEtLibelle($id,$mois);
$FraisHorsForfait = $pdo->getLesFraisHorsForfait($id, $mois);
$infoPdf = $pdo->getLesInfosPdf($id, $mois);
$pdf->SetFont('Arial', 'I', 14);


$pdf->Cell(40, 10, "Visiteur : ");
$pdf->Cell(60, 10, $infoPdf['nom']. " " .$infoPdf['prenom']);
$pdf->Ln(10);
$pdf->Cell(40, 10, "Mois : ");
$pdf->Cell(60, 10, $infoPdf['mois']);
$pdf->Ln(10);

$pdf->SetTextColor(0,0,255);
$pdf->Cell(90,10, "Frais forfaitaires",1,'C');
$pdf->Cell(30, 10, "quantite",1,'C');
$pdf->Cell(30, 10, "montant",1,'C');
$pdf->Cell(30, 10, "total",1,'C');
$pdf->Ln(10);

$pdf->SetFont('Arial',"", 11);
$pdf->SetTextColor(0);
foreach($fraisForfait as $unFraisForfait){
    $pdf->Cell(90,10, $str = utf8_decode($unFraisForfait['libelle']),1);
    $pdf->Cell(30, 10, $unFraisForfait['quantite'],1);
    $pdf->Cell(30, 10, $unFraisForfait['montant'],1);
    $pdf->Cell(30,10, ($unFraisForfait['montant'] * $unFraisForfait['quantite']),1);
    $pdf->Ln(10);
}

$pdf->SetFont('Arial', 'I', 14);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(100,10, "Autres Frais",2);
$pdf->Ln(10);

$pdf->Cell(40, 10, "Date",1);
$pdf->Cell(100, 10, $str = utf8_decode("Libellé"),1);
$pdf->Cell(40, 10, "Montant",1);
$pdf->Ln(10);


$pdf->SetFont('Arial',"", 11);
$pdf->SetTextColor(0);
 foreach($FraisHorsForfait as $unFraisHorsForfait){
    $pdf->Cell(40, 10, $unFraisHorsForfait['date'],1);
    $pdf->Cell(100,10, $str = utf8_decode($unFraisHorsForfait['libelle']),1);
    $pdf->Cell(40,10, $unFraisHorsForfait['montant'],1);
    $pdf->Ln(10);    
}
$pdf->Ln(10);
$pdf->Cell(100);
$pdf->Cell(40, 10, "Total", 1);
$pdf->Cell(40,10,$infoPdf['montantvalide'],1);
 
$pdf->Output();
}