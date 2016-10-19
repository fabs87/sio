<div id="contenu">
    
    <form method="post" action="index.php?uc=validerFrais&action=validationFrais">
          
        <?php echo $numMois."/".$numAnnee;?>
        <div class="piedForm">
            <p>
                <input id="mois" type="hidden" name="mois" value="<?php echo $numAnnee.$numMois;?>"/>
                <input id="ok" type="submit" value="Valider la Feuille de Frais" size="20" />
            </p> 
        </div>

    </form>
