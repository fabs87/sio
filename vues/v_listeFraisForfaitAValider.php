<div id="contenu">
    <h2>Frais a valider <?php echo $numMois . "-" . $numAnnee ?></h2>

    <form method="post" action="index.php?uc=validerFrais&action=validerMajFraisForfait">
        
            <fieldset>
                <legend>Eléments forfaitisés
                </legend>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    
                    $idFrais = $unFrais['idfrais'];
                    $libelle = $unFrais['libelle'];
                    $quantite = $unFrais['quantite'];
                    ?>
                    <p>
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" name="lesFrais[<?php echo $idFrais ?>]" size="10" maxlength="5" value="<?php echo $quantite ?>" >
                    </p>
                   
                <?php
                }
                ?>                    
            </fieldset>
        <input type="hidden" id="TxtVisiteur" name="leVisiteur" value="<?php echo $visiteur ?>">
        
        <div class="piedForm">
            <p>
                <input id="ok" type="submit" value="Valider" size="20" />
                <input id="annuler" type="reset" value="Effacer" size="20" />
            </p> 
        </div>

    </form>
