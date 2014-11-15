<?php 
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

function format_json($json, $html = false, $tabspaces = null){
    $tabcount = 0;
    $result = '';
    $inquote = false;
    $ignorenext = false;
    if ($html) {
        $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
        $newline = "<br/>";
    } else {
        $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
        $newline = "\n";
    }
    for($i = 0; $i < strlen($json); $i++) {
           $char = $json[$i];

           if ($ignorenext) {
               $result .= $char;
               $ignorenext = false;
           } else {
               switch($char) {
                   case ':':
                       $result .= $char . (!$inquote ? " " : "");
                       break;
                   case '{':
                       if (!$inquote) {
                           $tabcount++;
                           $result .= $char . $newline . str_repeat($tab, $tabcount);
                       }
                       else {
                           $result .= $char;
                       }
                       break;
                   case '}':
                       if (!$inquote) {
                           $tabcount--;
                           $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                       }
                       else {
                           $result .= $char;
                       }
                       break;
                   case ',':
                       if (!$inquote) {
                           $result .= $char . $newline . str_repeat($tab, $tabcount);
                       }
                       else {
                           $result .= $char;
                       }
                       break;
                   case '"':
                       $inquote = !$inquote;
                       $result .= $char;
                       break;
                   case '\\':
                       if ($inquote) $ignorenext = true;
                       $result .= $char;
                       break;
                   default:
                       $result .= $char;
               }
        }
    }
    return $result;
}
function send($url,$post=false,$ref=false,$follow=false,$json=false){
    $cookies = getcwd().'/cookies';
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,"; 
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
    $header[] = "Cache-Control: max-age=0"; 
    $header[] = "Connection: keep-alive"; 
    $header[] = "Keep-Alive: 300"; 
    if($json){
        $header[] = "X-Request: JSON";
    }
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3"; 
    $header[] = "Accept-Language: en-US,en;q=0.8 "; 
    $header[] = "Pragma: ";
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookies);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookies);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
    curl_setopt($ch,CURLOPT_USERAGENT,'Access-Token');
    if($post){
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
    }
    if($follow) curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    if($ref) curl_setopt($ch,CURLOPT_REFERER,$ref);
    $return = curl_exec($ch);
    curl_close($ch);
    if(file_exists($cookies)) unlink($cookies);
    return $return;
}
?>