<?php
include(dirname(__FILE__).'/app/classes/class.Fortunes.sqlite.php');
include(dirname(__FILE__).'/custom/config.php');

/**
 * Filtrage du $_POST
 */
$texte = (isset($_POST['texte'])) ? $_POST['texte'] : '';
if (get_magic_quotes_gpc()){$texte = stripslashes($texte);}
$nickname = (isset($_POST['nickname'])) ? $_POST['nickname'] : 'Anonyme';
$pre = (isset($_POST['pre'])) ? 'pre' : 'normal';
$pre_checked = (isset($_POST['pre'])) ? ' checked="checked"' : '';
$affichage = '';

/**
 * Ecrire le fichier
 */
if (isset($_POST['save'])){
    if (empty($_POST['email'])){
        $cookie_jar = new CookieJar($fortunes_config);
        $cookie_jar->addFortune($nickname, $texte, $pre);
        header('Location: index.php');
        exit;
    }
}
else if (isset($_POST['preview'])){
    $meta = Array(
        'id' => 0,
        'author' => $nickname,
        'date' => date("Y-m-d H:i:s"),
        'mode' => $pre,
        'online' => 1,
        'fortune' => $texte,
        'vote' => 0
    );
    
    $cookie = new FortuneCookie($meta, $fortunes_config);
    $affichage = $cookie->getHTML();
}
header('Content-type: text/html;charset=ISO-8859-15');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Language" content="fr" />

    <title>Fortunes</title>
    <link rel="stylesheet" type="text/css" href="custom/style/fortunes.css" media="screen" />
    <style type="text/css">
    label{
        display: block;
    }
    </style>
</head>

<body>
<h1>Ajouter une fortune</h1>
<p>Collez l'extrait de log &agrave; publier. Le format est le suivant:</p>
<pre>
&lt;toi&gt; kikoololmdr
&lt;moi&gt; stfu!
</pre>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <fieldset>
    <label>Nickname:</label>
    <input type="text" name="nickname" value="<?php echo $nickname ?>" />
    <label>Texte</label>
    <textarea name="texte" rows="15" cols="80"><?php echo $texte ?></textarea>
    <label><input type="checkbox" name="pre" value="pre" <?php echo $pre_checked ?> /> Texte préformaté</label>
    <p style="position: absolute; top: -100em;">
        <label>Laisser vide (antispam): <input type="text" name="email" id="email" value="" /></label>
    </p>
    <p>
    <input type="submit" name="preview" value="Previsualiser" />
    <input type="submit" name="save" value="Enregistrer" />
    </p>
    </fieldset>
</form>
<div>
<?php echo $affichage; ?>
</div>
</body>
</html>