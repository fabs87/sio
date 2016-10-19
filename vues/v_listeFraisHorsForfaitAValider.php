
<table class="listeLegere">
    <caption>Descriptif des éléments hors forfait
    </caption>
    <tr>
        <th class="date">Date</th>
        <th class="libelle">Libellé</th>  
        <th class="montant">Montant</th>  
        <th class="action">&nbsp;</th>
        <th class="action">&nbsp;</th>
    </tr>

    <?php
    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        $libelle = $unFraisHorsForfait['libelle'];
        $date = $unFraisHorsForfait['date'];
        $montant = $unFraisHorsForfait['montant'];
        $id = $unFraisHorsForfait['id'];
        ?>		
        <tr>
            <td> <?php echo $date ?></td>
            <td><?php echo $libelle ?></td>
            <td><?php echo $montant ?></td>
            <td><a href="index.php?uc=validerFrais&action=refuserFrais&idFrais=<?php echo $id ?>" 
                   onclick="return confirm('Voulez-vous vraiment Refuser ce frais ?');">Refuser ce frais</a></td>
            <td><a href="index.php?uc=validerFrais&action=reporterFrais&idFrais=<?php echo $id ?>" 
                   onclick="return confirm('Voulez-vous vraiment reporter ce frais ?');">Reporter ce frais</a></td>
        </tr>
        <?php
    }
    ?>	  

</table>

</div>


