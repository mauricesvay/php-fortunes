<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />

    <title><?php echo FORTUNES_NAME ?></title>
    <link rel="stylesheet" type="text/css" href="custom/style/fortunes.css" media="screen" />
    <link rel="alternate" type="application/atom+xml" title="Atom" href="atom10.php" />
    <link rel="alternate" type="text/plain" title="Fortune format" href="fortune.php" />

    <script type="text/javascript" src="app/js/prototype-1.4.0.js"></script>
    <script type="text/javascript" src="app/js/fortunes.js"></script>
</head>

<body>
    <div id="page">
        <?php include(dirname(__FILE__).'/top.tpl.php'); ?>

        <div id="content">
        <?php
        $votes = CookieJar::getVotes();
        if (isset($cookie_jar->cookies)){
            foreach ($cookie_jar->cookies as $cookie){
                include(dirname(__FILE__).'/fortune.tpl.php');
            }
        }
        ?>
        </div>

        <?php include(dirname(__FILE__).'/footer.tpl.php'); ?>
    </div>
</body>
</html>