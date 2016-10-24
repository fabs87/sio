 <div id="contenu">
    <h2>Liste des fiches de frais par Utilisateur</h2>
    <h3>Visiteur et Mois à sélectionner : </h3>
    <form action="index.php?uc=validerFrais&action=voirLesFrais" method="post">
        <div class="corpsForm">
            <p>
                <label for="lstVisiteur" accesskey="n">Visiteur : </label>
                <select id="lstVisiteur" name="lstVisiteur">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $visiteur = $unVisiteur['id'];
                        ?>	
                        <option selected value="<?php echo $visiteur ?>"><?php echo $unVisiteur['nom']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </p>	             
            <label for="lstMois" accesskey="n">Mois : </label>
            <select id="lstMois" name="lstMois">
                <?php
                foreach ($lesMois as $unMois) {
                    $mois = $unMois['mois'];
                    var_dump($mois);
                    ?>

                    <option selected value="<?php echo $mois ?>"><?php echo $mois ?> </option>
                    <?php
                }
                ?>
            </select>


        </div>
        <div class="piedForm">
            <p>
                <input id="ok" type="submit" value="Valider" size="20" />
                <input id="annuler" type="reset" value="Effacer" size="20" />
            </p> 
        </div>

    </form>