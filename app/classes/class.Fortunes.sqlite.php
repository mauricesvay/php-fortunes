<?php
/**
 * Fortune Cookies
 *
 * Ingrédients :
 * * 1 blanc d'oeuf
 * * 1/2 tasse de farine
 * * 1/4 de cuillère à café de gingembre ou vanille
 * * 1/4 tasse de sucre
 * * une pincée de sel
 * * 1 cuillière à café de beurre fondu
 *
 * Préparation :
 * 
 * - Préchauffer le four à 350°C;
 * - Battre l'oeuf, ajouter le sucre;
 * - Mélanger les ingrédients restant en gardant la farine pour la fin;
 * - Beurrer un plat à biscuit (ou papier sulfurisé) et y déposer la pâte 
 *   en petits tas;
 * - Faire cuire à 300°C environ 5 minutes;
 * - Sortir un par un les cookies, ajouter la fortune, plier le cookie en deux.
 */
 
/******************************************************************************
 * Classe FortuneConfig                                                       * 
 ******************************************************************************/
class FortunesConfig{
    var $fortunes_filename;
    
    var $users_filename;
    var $colors_filename;
    
    var $users;
    var $colors;
    
    function FortunesConfig($fortunes_filename,$users_filename,$colors_filename){
        $this->fortunes_filename = $fortunes_filename;
        $this->users_filename = $users_filename;
        $this->colors_filename = $colors_filename;
        
        $this->users = Array();
        $this->_loadUsers($users_filename);
        $this->colors = Array();
        $this->_loadColors($colors_filename);
    }

    function _loadUsers($filename){
        if (is_file($filename)){
            $fp = fopen ($filename,"r");
            while ($data = fgetcsv($fp, 1000, ",")){
                $this->users[$data[0]]['url'] = $data[1];
                if (isset($data[2])) {
                    $this->users[$data[0]]['color'] = $data[2];
                }
            }
            fclose ($fp);
            return count($this->users);
        }
        return false;
    }
    
    function _loadColors($filename){
        $colors = file($filename);
        foreach ($colors as $color){
            if (trim($color) != ''){
                $this->colors[] = $color;
            }
        }
        return count($this->colors);
    }
    
    function getColor($string){
        $n = 0;
        $txt_sum = 0;
        
        //On essaie de trouver dans la liste des utilisateurs
        $keys = array_keys($this->users);
        foreach ($keys as $key) {
            if (preg_match ('/'.addslashes($key).'/i', $string)) {
                if (isset($this->users[$key]['color'])) {
                    return $this->users[$key]['color'];
                }
            }
        }
        
        //Sinon on hashe le pseudo pour obtenir la couleur
        $pseudocolors = array ( 19, 20, 22, 24, 25, 26, 27, 28, 29 );
        while ($n<strlen($string) && isset($string{$n})) {
            $txt_sum += ord($string{$n++});
        }
        $txt_sum %= count($pseudocolors);
        return $this->colors[$pseudocolors[$txt_sum]];
    }
}
 
/******************************************************************************
 * Classe CookieJar                                                           * 
 ******************************************************************************/
class CookieJar{
    var $config;
    var $cookies;
    var $conn;
    
    /**
     * Et si on faisait des fortunes cookies ?
     */
    function CookieJar($config){
        $this->config = $config;
        $this->conn = sqlite_open($this->config->fortunes_filename);
    }
    
    /**
     * Sort les fortune cookies du four
     */
    function load($id='all', $count='', $order='id DESC', $nick=''){
        $sql = "
            SELECT
                ROWID as id,
                author,
                date,
                mode,
                online,
                fortune,
                vote
            FROM 
                fortunes
            WHERE
                online = 1";

        if ($id != 'all'){
            $sql .= ' AND id = '.(int)$id;
        }
        
        if (!empty($nick)){
            $sql .= " AND fortune LIKE '%<".sqlite_escape_string($nick).">%'";
        }
        
        if (!empty($order)){
            $sql .= 'ORDER BY '.$order;
        }
        
        if (!empty($count)){
            $sql .= ' LIMIT '.$count;
        }
        
        $result = sqlite_query($sql, $this->conn);
        
        while ($row = sqlite_fetch_array($result)){
            $this->cookies[] = new FortuneCookie($row,$this->config);
        }
    }
    
    /**
     * Compte le nombre de fortune cookies dans la boîte
     */
    function count(){
        return count($this->cookies);
    }
    
    /**
     * Prend un fortune cookie au hasard
     */
    function getRandomFortune(){
        return array_rand($this->cookies);
    }
    
    /**
     * Enfourne un fortune cookie
     */
    function addFortune($author, $texte, $mode){
        
        $texte = str_replace("\r", '', $texte);
        
        $sql = "
            INSERT INTO
                fortunes (author, date, mode, online, fortune, vote)
            VALUES
                (
                    '".sqlite_escape_string($author)."', 
                    '".date("Y-m-d H:i:s")."', 
                    '".sqlite_escape_string($mode)."', 
                    1, 
                    '".sqlite_escape_string($texte)."',
                    0
                )";
                
        sqlite_query($sql, $this->conn);
        return sqlite_changes($this->conn);
    }
    
    /**
     * Dire que le cookie est vraiment miam
     */
    function vote($id, $inc=1){
        $votes = CookieJar::getVotes();
        
        if (!isset($votes[$id])) {
            //Update
            $sql = "
            UPDATE
                fortunes
            SET
                vote = vote + $inc
            WHERE
                ROWID = ".(int)$id;
            sqlite_query($sql, $this->conn);
            $votes[$id] = 1;
            setcookie('votes', addslashes(serialize($votes)), time()+60*60*24*365);
            setcookie('tricher','pas_bien', time()+60*60*24*365);
            
            //Nouvelle valeur
            $sql = "SELECT vote FROM fortunes WHERE ROWID = ".(int)$id;
            $result = sqlite_query($sql, $this->conn);
            if ($row = sqlite_fetch_array($result)){
                return $row['vote'];
            }
        } else {
            return 0;
        }
    }
    
    function getVotes(){
        $votes = Array();
        if (isset($_COOKIE['votes'])){
            $votes = unserialize(stripslashes($_COOKIE['votes']));
        }
        return $votes;
    }
    
    /**
     * On planque un cookie
     */
    function offline($id){
        $sql = "
        UPDATE
            fortunes
        SET
            online = 0
        WHERE
            ROWID = ".sqlite_escape_string($id);
        sqlite_query($sql, $this->conn);
        return sqlite_changes($this->conn);
    }
    
    /**
     * On restitue un cookie planqué
     */
    function online($id){
        $sql = "
        UPDATE
            fortunes
        SET
            online = 1
        WHERE
            ROWID = ".sqlite_escape_string($id);
        sqlite_query($sql, $this->conn);
        return sqlite_changes($this->conn);
    }
    /*
    function convertCookies(){
        $votes = Array();
        
        //Convert old cookies to new cookies
        if (isset($_COOKIE['fortunes'])){
            $votes = $_COOKIE['fortunes'];
            setcookie('votes', addslashes(serialize($votes)), time()+60*60*24*365);
            //Make them available now
            $_COOKIES['votes'] = $votes;
        }
    }
    */
}

/******************************************************************************
 * Classe FortuneCookie                                                       * 
 ******************************************************************************/
class FortuneCookie{
    var $config;

    var $id;
    var $header;
    var $content;
    
    var $users;
    
    function FortuneCookie($data, $config){
        $this->config = $config;
        
        $this->id = $data['id'];
        $this->header['author'] = $data['author'];
        $this->header['date'] = strtotime($data['date']);
        $this->header['mode'] = $data['mode'];
        $this->header['online'] = $data['online'];
        $this->header['vote'] = $data['vote'];
        $this->content = explode("\n",$data['fortune']);
    }
    
    /**
     * Methodes "publiques"
     **************************************************************************/    

    function getId(){
        return $this->id;
    }
    
    function getAuthor(){
        return $this->header['author'];
    }
    
    function getDate($format = 'Y-m-d H:i:s'){
        return date($format, $this->header['date']);
    }
    
    function getMode(){
        return $this->header['mode'];
    }
    
    function getVote(){
        return $this->header['vote'];
    }
    
    function getRawContent(){
        return implode("\n", $this->content);
    }
    
    function getHTML(){
        $result = '';
        $result.= '<dl class="irc '.$this->getMode()."\">\n";
        foreach ($this->content as $line){
            if (trim($line) != ''){
                
                //Enlève l'heure
                $line = FortuneCookie::_filterRemoveDate($line);
                
                if ($parts = FortuneCookie::_isSpeech($line)){
                    //le nickname passe à travers plusieurs filtres successifs
                    $line_nick = FortuneCookie::_filter(
                                        $parts[1],
                                        array(
                                            '_filterRemoveBrackets',
                                            '_filterAddNickColor',
                                            '_filterAddNickLinks',
                                        )
                                 );
                    //le speech passe à travers plusieurs filtres successifs
                    $line_speech = FortuneCookie::_filter(
                                        htmlentities($parts[2]),
                                        array(
                                            '_filterMakeURLLinks',
                                            '_filterTrimFirstSpace'
                                        )
                                 );
                    $result.= "<dt>".utf8_encode($line_nick)."</dt>\n";
                    $result.= "<dd>".utf8_encode($line_speech)."</dd>\n";
                }
                else{
                    $result.= "<dt>&nbsp;</dt>\n";
                    $result.= "<dd>".utf8_encode(htmlspecialchars($line))."</dd>\n";
                }
            }
        }
        $result.= "</dl>\n";
        return $result;
    }
    
    function getAtomEntry(){
        $result = '
        <entry xml:lang="fr">
        <title>fortune '.$this->getId().'</title>
        <link rel="alternate" href="'.FORTUNES_URL.'?view=one&amp;id='.$this->getId().'"/>
        <issued>'.$this->getDate("Y-m-d\TH:i:s\Z").'</issued>
        <updated>'.$this->getDate("Y-m-d\TH:i:s\Z").'</updated>
        <author><name>'.$this->getAuthor().'</name></author>
        <content type="text/html" mode="escaped">
        <![CDATA[
        '.$this->getHTML().'
        ]]>
        </content>
        </entry>';
        return $result;
    }
    
    function getAtom10Entry(){
        $url_parts = parse_url(FORTUNES_URL);
        
        $result = '
        <entry xmlns="http://www.w3.org/2005/Atom">
        <title type="html">fortune '.$this->getId().'</title>
        <id>tag:'.$url_parts['host'].','.$this->getDate("Y").':'.$this->getId().'</id>
        <link rel="alternate" href="'.FORTUNES_URL.'?view=one&amp;id='.$this->getId().'"/>
        <published>'.$this->getDate("Y-m-d\TH:i:s\Z").'</published>
        <updated>'.$this->getDate("Y-m-d\TH:i:s\Z").'</updated>
        <author><name>'.$this->getAuthor().'</name></author>
        <content type="html">
        <![CDATA[
        '.$this->getHTML().'
        ]]>
        </content>
        </entry>';
        return $result;
    }
    
    /**
     * Methodes "privées"
     **************************************************************************/
    function _parseHeader($string){
        $parts = explode(',', $string);
        $header = Array(
            'author' => trim($parts[0]),
            'date' => strtotime(trim($parts[1])),
            'mode' => (isset($parts[2])) ? trim($parts[2]) : 'normal'
        );
        return $header;
    }
    
    function _isSpeech($string){
        if (preg_match('/^(<[^<>]+>)(.*)/',$string, $matches)){
            return $matches;
        }
        return false;
    }
    
    function _isThought($string){
        return preg_match('/^\*.*/', $string);
    }
    
    function _isMovement($string){
        return preg_match('/^(<--|-->).*/', $string);
    }

    /**
     * Filtrage
     **************************************************************************/
    
    function _filter($string, $filters_names){
        foreach($filters_names as $filter_name){
            $string = FortuneCookie::$filter_name($string);
        }
        return $string;
    }

    /**
     * Enlève le premier et le dernier caractère
     */
    function _filterRemoveBrackets($string){
        if ($string == ''){
            return $string;
        }
        else{
            return substr($string,1,-1);
        }
    }
    
    /**
     * Enlève l'heure
     */
    function _filterRemoveDate($string){
        return preg_replace('/^\[\d+:\d+\]\s+/','', $string);
    }
    
    /**
     * Transforme les URL (http/https) en liens
     */
    function _filterMakeURLLinks($string){
        $string = preg_replace('`(http)+(s)?:(//)(\S+)?`i', '<a href="${0}" rel="nofollow">${0}</a>', $string);
        return $string;
    }
    
    /**
     * Ajoute de la couleur à un pseudo
     * FIXME : découper ce filtre en deux pour ajouter les <> séparément
     */
    function _filterAddNickColor($string){
        return '&lt;<span style="color:'.$this->config->getColor($string).'">'.$string.'</span>&gt;';
    }
    
    /**
     * Ajoute les liens aux pseudos
     */
    function _filterAddNickLinks($string){
        if (count($this->config->users) > 0) {
            $keys = array_keys($this->config->users);
            foreach ($keys as $key) {
                $search[] = '/('.addslashes($key).')/i';
                $replace[] = '<a href="'.$this->config->users[$key]['url'].'">${1}</a>';
            }
            $string = preg_replace($search, $replace, $string);
        }
        return $string;
    }
    
    /**
     * Enlève le premier espace d'une chaine
     */
    function _filterTrimFirstSpace($string){
        if (!empty($string)){
            if ($string{0} == ' '){
                return substr($string, 1);
            }
        }
        return $string;
    }
}
?>
