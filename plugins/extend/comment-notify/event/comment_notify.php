<?php

namespace Sunlight;

use Sunlight\Database\Database as DB;
use Sunlight\Email;

return function(array $args) {
    $mailText = "Dobrý den,\nna webové stránky ".Template::siteTitle()." (".Template::siteUrl().")";
    $send = false;
    $user = "";
    
    if($args['posttype'] == 1 || $args['posttype'] == 3 || $args['posttype'] == 5)
    {
        $title = DB::result(DB::query("SELECT title FROM " . DB::table('page') . "  WHERE id=".$args['posttarget']));
        if($args['posttype'] == 1) $mailText .= " byl k sekci ".$title." přidán nový příspěvek ";
        if($args['posttype'] == 3) $mailText .= " byl ke knize ".$title." přidán nový příspěvek ";
        if($args['posttype'] == 5) $mailText .= " bylo k fóru ".$title." přidáno nové téma s předmětem ".$args['subject'];
        $send = true;
    }
    if($args['posttype'] == 2)
    {
        $title = DB::result(DB::query("SELECT title FROM " . DB::table('article') . " WHERE id=".$args['posttarget']), 0);
        $mailText .= " byl k článku ".$title." přidán nový komentář";
        $send = true;
    }
    if($send)
    {
        if($args['author'] == -1) $mailText .= " od hosta ".$args['guest'].".\n";
        else
        {
            $user = DB::result(DB::query("SELECT username FROM " . DB::table('user') . " WHERE id=".$args['author']), 0);
            $mailText .= " od uživatele ".$user.".\n";
        }

        for($i = 0; $i < 30; $i++)
            $mailText .= "-";
 
        $mailText .= "\n".$args['text'];   
    
        if (!$this->getConfig()['adresát_upozornění'] || $this->getConfig()['adresát_upozornění'] == "default@mail.com") 
            return;
        else
            Email::send($this->getConfig()['adresát_upozornění'], "Nový příspěvek na webu", $mailText);
    }
};
