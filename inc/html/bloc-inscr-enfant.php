<div class="form-block block-ajout">
     <?php if(isset($hasNoEnfant)): ?>
    <div>
    <?php elseif($compt > 0): ?>
        <div class="non-editable">
                <span class="btn btn-blue toogle-hide-ajout"><span class="glyphicon glyphicon-plus-sign"></span> Ajouter un enfant à la liste déroulante</span>
        </div>
        <div class="editable">
            <h4>Ajouter un enfant à la liste</h4>
    <?php else: ?>
        <div>
    <?php endif; ?>
        <div class="form-group">
            <label for="nom_faj" class="control-label">Nom de Famille de l'enfant* :</label>
            <input required type="text" class="form-control req" name="nom_faj[]"/>
        </div>
        <div class="form-group">
            <label for="pren_faj" class="control-label">Prénom* :</label>
            <input required type="text" class="form-control req" name="pren_faj[]" />
        </div>
        <div id="radioS" class="form-group">
            <input type="radio" name="sex_faj[]" value="fille" id="fille" checked="checked" /> <label for="fille">Fille</label>
            <input type="radio" name="sex_faj[]" value="garco" id="garco" /> <label for="garco">Garçon</label>
        </div>
        <div class="clr"></div>

        <label for="daten_f">Date de naissance* :<br/><small>(Au format JJ/MM/AAAA)</small></label>
        <div class="form-group dates">
            <input class="req form-control" required type="number" name="date_jaj[]" size="2" data-day="day" <?php echo $errorMessDate; ?>/>
            <div class="help-block with-errors"></div>
        </div>
        <div class="form-group dates">
            <span>/</span><input class="req form-control" required type="number" name="date_maj[]" size="2" data-month="month" <?php echo $errorMessDate; ?>/>
            <div class="help-block with-errors"></div>
        </div>
        <div class="form-group dates">
            <span>/</span><input class="req form-control" required type="number" name="date_aaj[]" size="4" data-year="year" <?php echo $errorMessDate; ?>/>
            <div class="help-block with-errors"></div>
        </div>
        <?php if(isset($ligne)){ echo '<input type="hidden" name="lgne_cde[]" value="'.$ligne['ins']->ligne_cde().'"/>';}?>
        <input type="hidden" name="inscenfaj[]" value="<?php echo $compt; ?>">
        <?php if(isset($hasNoEnfant)): ?>
            <input type="hidden" name="action-ajoutEn[]" value="1" class="trigger-form">
        <?php elseif($compt > 0): ?>
            <input type="hidden" name="action-ajoutEn[]" value="0" class="trigger-form">
            <div href="#" class="btn btn-blue toogle-hide-annuler"><span class="glyphicon glyphicon-minus-sign"></span> Annuler</div>
        <?php else: ?>
            <input type="hidden" name="action-ajoutEn[]" value="1" class="trigger-form">
        <?php endif; ?>
    </div>
</div>