<h1><a href="index.php"><img src="custom/img/h1.png" alt="Fortunes" /></a></h1>

<ul class="tabs">
<li><a href="./">30 derni&egrave;res</a></li>
    <li><a href="?view=top">Top 30</a></li>
    <li><a href="?view=bottom">Flop 30</a></li>
    <li><a href="?view=all">Toutes</a></li>
    
    <li style="float: right"><a href="add.php" title="Ajouter une nouvelle fortune">Ajouter</a></li>
    <li style="float: right"><a href="atom10.php" title="Flux ATOM 1.0">Atom 1.0</a></li>
    <li style="float: right"><a href="fortune.php" title="T&eacute;l&eacute;charger les fortunes">\n%\n</a></li>
</ul>

<div id="search">
    <form action="" method="get">
        <fieldset>
            <input type="hidden" class="hidden" name="view" id="view" value="featuring"/>
            <label for="nick">Pseudo :</label>
            <input type="text" class="text" value="<?php echo isset($_GET['nick'])?$_GET['nick']:'' ?>" name="nick" id="nick"/>
            <input type="submit" class="submit" value="Rechercher" />
        </fieldset>
    </form>
</div>