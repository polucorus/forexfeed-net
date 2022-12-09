<?php
/**
 * ForexFeed.Net Data API
 *
 * Copyright 2016 ForexFeed.Net <copyright@forexfeed.net>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *      This product includes software developed by ForexFeed.Net.
 * 4. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * // USAGE EXAMPLE
 * // -----------------------------------------------------------
 *
 * <?php
 *
 * // Load the ForexFeed.net API
 * require_once('ForexFeed.class.php');
 *
 * //  Create the ForexFeed Object
 * $fxfeed = new ForexFeed(
 *          array(
 *            'app_id' => 'YOUR_APP_ID',
 *            'symbol'     => 'EURUSD,GBPUSD,USDCHF,USDCAD,AUDUSD',
 *            'interval'   => 3600,
 *            'periods'    => 1,
 *            )
 *           );
 * 
 * //  Request the Data
 * $fxfeed->getData();
 *
 * print "Number of Quotes: " . $fxfeed->getNumQuotes() . "<br><br>\n";
 * print "Copyright: " . $fxfeed->getCopyright() . "<br>\n";
 * print "Website: " . $fxfeed->getWebsite() . "<br>\n";
 * print "License: " . $fxfeed->getLicense() . "<br>\n";
 * print "Redistribution: " . $fxfeed->getRedistribution() . "<br>\n";
 * print "AccessPeriod: " . $fxfeed->getAccessPeriod() . "<br>\n";
 * print "AccessPerPeriod: " . $fxfeed->getAccessPerPeriod() . "<br>\n";
 * print "AccessThisPeriod: " . $fxfeed->getAccessThisPeriod() . "<br>\n";
 * print "AccessRemainingThisPeriod: " . $fxfeed->getAccessPeriodRemaining() . "<br>\n";
 * print "AccessPeriodBegan: " . $fxfeed->getAccessPeriodBegan() . "<br>\n";
 * print "NextAccessPeriodStarts: " . $fxfeed->getAccessPeriodStarts() . "<br>\n";
 * print "<br>\n";
 * 
 * 
 * //  Display the data
 * if( $fxfeed->getStatus() == "OK" ){
 *  while( $fxfeed->iterator() ){
 *    
 *    print " Symbol: " . $fxfeed->iteratorGetSymbol();
 *    print " Title: " . $fxfeed->iteratorGetTitle();
 *    print " Time: " . $fxfeed->iteratorGetTimestamp();
 *    
 *    if( $fxfeed->getInterval() == 1 ){
 *     if( $fxfeed->getPrice() == 'bid,ask' ){
 *      print " Bid: " . $fxfeed->iteratorGetBid();
 *      print " Ask: " . $fxfeed->iteratorGetAsk();
 *      }
 *     else{
 *      print " Price: " . $fxfeed->iteratorGetPrice();
 *      }
 *     }
 *    else{
 *     print " Open: " . $fxfeed->iteratorGetOpen();
 *     print " High: " . $fxfeed->iteratorGetHigh();
 *     print " Low: " . $fxfeed->iteratorGetLow();
 *     print " Close: " . $fxfeed->iteratorGetClose();
 *     }
 *    print "<br>\n";
 *    
 *    }
 *  }
 * else{
 *  print "Status: " . $fxfeed->getStatus() . "<br>\n";
 *  print "ErrorCode: " . $fxfeed->getErrorCode() . "<br>\n";
 *  print "ErrorMessage: " . $fxfeed->getErrorMessage() . "<br>\n";
 *  }
 *
 * ?>
 *
 * // -----------------------------------------------------------
 * // END USAGE
 *
 */
namespace ForexFeedNet;

class ForexFeed {
  
  private $version = '2.3';
  private $debug = 0;

  private $host = 'http://api.forexfeed.net';
  private $port = '80';
  private $timeout = '60';
  
  private $app_id = '';
  private $symbol = 'EURUSD';
  private $interval = '3600';
  
  private $periods = 1;
  private $starttime = 0;
//   private $endtime = 0;
  private $price = '';
  private $fields = "";
  private $agree = 0;
  private $no_stats = 0;
  
  private $pos = -1;
  private $cache = '';
  private $quotes = array();
  
  private $all_symbols = array();
  private $all_intervals = array();
  
  /*  Constructor function  */
  public function __construct($flags) {
    $this->app_id = (isset($flags['app_id'])) ? $flags['app_id'] : '';
    if( isset($flags['access_key']) ) {
     $this->app_id = $flags['access_key'];
     }
    
    $this->symbol = (isset($flags['symbol'])) ? $flags['symbol'] : 'EURUSD';
    $this->interval = (isset($flags['interval'])) ? $flags['interval'] : 3600;
    
    $this->periods = (isset($flags['periods'])) ? $flags['periods'] : 1;
    $this->starttime = (isset($flags['starttime'])) ? $flags['starttime'] : 0;
//     $this->endtime = (isset($flags['endtime'])) ? $flags['endtime'] : 0;
    }

public function getHostname(){
  return $this->host;
}
public function setHostname($host){
  $this->host = $host;
  return $host;
}

public function getPort(){
  return $this->port;
}
public function setPort($port){
  $this->port = $port;
  return $port;
}

public function getTimeout(){
  return $this->timeout;
}
public function setTimeout($timeout){
  $this->timeout = $timeout;
  return $timeout;
}





public function getAppID() {
  return $this->app_id;
}
public function setAppID($app_id) {
  $this->app_id = $app_id;
  return $app_id;
}

##
## DEPRECATED: Please use the getAppID function instead of getAccessKey
##
public function getAccessKey() {
  return $this->getAppID();
}
##
## DEPRECATED: Please use the setAppID function instead of setAccessKey
##
public function setAccessKey($app_id) {
  return $this->setAppID($app_id);
}








public function getSymbol(){
  return $this->symbol;
}
public function setSymbol($symbol){
  $this->symbol = $symbol;
  return $symbol;
}

public function getInterval(){
  return $this->interval;
}
public function setInterval($interval){
  $this->interval = $interval;
  return $interval;
}



public function getPeriods(){
  return $this->periods;
}
public function setPeriods($periods){
  $this->periods = $periods;
  return $periods;
}

public function getStartTime(){
  return $this->starttime;
}
public function setStartTime($time){
  $this->starttime = $time;
  return $time;
}

// public function getEndTime(){
//   return $this->endtime;
// }
// public function setEndTime($time){
//   $this->endtime = $time;
//   return $time;
// }

public function getPrice(){
  return $this->price;
}
public function setPrice($price){
  $this->price = $price;
  return $price;
}

public function getFields(){
  return $this->fields;
}
public function setFields($fields){
  $this->fields = $fields;
  return $fields;
}

/**
 * setAgree - Set the Agree To Terms Flag.
 * 
 * This may be used to suppress the License statement in Server responses.
 * 
 * Setting agree to a value of 1 signifies your agreement to the
 * terms within, in no way does it alter your license, rights and/or limitations
 * and you agree to be bound by any existing license agreement.
 *
 * This may be useful in low-bandwidth conditions, for example. Or simply to
 * conserve some bytes on each request/response.
 */
public function setAgree($agree){
  $this->agree = $agree;
  return $agree;
}
public function getAgree(){
  return $this->agree;
}

/**
 * setNoStats - Set the No-Stats Flag.
 * 
 * This may be used to suppress the Account Stats in getData() responses by
 * setting the NoStats flag to a value of 1.
 * 
 * This may be useful in low-bandwidth conditions, for example. Or simply to
 * conserve some bytes on each request/response.
 * 
 * Note however that the Account Statistics information (usually available
 * through the various getAccess* functions) will not be available on Data
 * Requests.
 */
public function setNoStats($no_stats){
  $this->no_stats = $no_stats;
  return $no_stats;
}
public function getNoStats(){
  return $this->no_stats;
}


/*
 * Send the request to the host
 */
private function doRequest($query_string) {
  
  $result = new stdClass();
  
  $host = $this->getHostname();
  $port = $this->getPort();
  
  if($this->debug) print "Request: $host/$query_string<br>\n";
  
  $method = 'GET';
  $errno = '';
  $errstr = '';
  
  if( $this->getAgree() == 1 ) {
    $query_string .= '/atos-Y';
   }
  
  if( $this->getNoStats() == 1 ) {
    $query_string .= '/ns-Y';
   }
  
  $query_string .= '/cv-'. $this->version;
  $query_string .= '/al-ph';
  
  $query_string .= '/xr-'. rand();
  
  // Parse the URL and make sure we can handle the scheme.
  $uri = parse_url($host);
  
  if ($uri == FALSE) {
   $result->error = 'Unable to parse URL: ' . $host;
   return $result;
   }
  
  if (!isset($uri['scheme'])) {
   $result->error = 'Missing scheme: ' . $host;
   return $result;
   }
  
  $req_host = $uri['host'];
  if ($port > 0 && $port != 80) {
   $req_host .= ':' . $port;
   }
  
  switch ($uri['scheme']) {
    case 'http':
      $fp = @fsockopen($uri['host'], $port, $errno, $errstr, $this->getTimeout());
      break;
    case 'https':
      // Note: Only works for PHP 4.3 compiled with OpenSSL.
      $fp = @fsockopen('ssl://'. $uri['host'], $port, $errno, $errstr, $this->getTimeout());
      break;
    default:
      $result->error = 'invalid scheme '. $uri['scheme'];
      return $result;
    }

  // Make sure the socket opened properly.
  if (!$fp) {
   $result->code = $errno;
   $result->error = trim($errstr);
   return $result;
   }

  // Create HTTP request.
  $headers = array(
    'Connection' => 'Connection: close',
    'User-Agent' => 'User-Agent: ForexFeed-PHP-'.$this->version,
    // Host:  RFC 2616: "non-standard ports MUST, default ports MAY be included".
    'Host' => "Host: $req_host",
    'Content-Length' => 'Content-Length: 0'
  );
  
  $request = $method .' /'. $query_string ." HTTP/1.1\r\n";
  $request .= implode("\r\n", $headers);
  $request .= "\r\n\r\n";
  
  fwrite($fp, $request);
  
  // Fetch response.
  $res = '';
  while( !feof($fp) && $chunk = fread($fp, 1024) ) {
    $res .= $chunk;
    }
  fclose($fp);
  
  // Parse response.
  list($split, $result->data) = explode("\r\n\r\n", $res, 2);
  $split = preg_split("/\r\n|\n|\r/", $split);
  
  list($protocol, $code, $text) = explode(' ', trim(array_shift($split)), 3);
  $result->headers = array();
  
  // Parse headers.
  while ($line = trim(array_shift($split))) {
    list($header, $value) = explode(':', $line, 2);
    if (isset($result->headers[$header]) && $header == 'Set-Cookie') {
     // RFC 2109: the Set-Cookie response header comprises the token Set-
     // Cookie:, followed by a comma-separated list of one or more cookies.
     $result->headers[$header] .= ','. trim($value);
     }
    else {
     $result->headers[$header] = trim($value);
     }
  }
  
  if( $result->headers['Transfer-Encoding'] && $result->headers['Transfer-Encoding'] == 'chunked' ){
   $result->data = $this->decode_chunked($result->data);
   }
  
  if( $code != 200 ){
   $result->error = $text;
   }
  
  $result->code = $code;
  return $result;
}


// private function decode_chunked_old($str) {
//   for ($res = ''; !empty($str); $str = trim($str)) {
//     $pos = strpos($str, "\r\n");
//     $len = hexdec(substr($str, 0, $pos));
//     $res.= substr($str, $pos + 2, $len);
//     $str = substr($str, $pos + 2 + $len);
//     }
//   return $res;
// }

private function decode_chunked($in) {
  $out = '';
  while($in != '') {
    $lf_pos = strpos($in, "\012");
    if($lf_pos === false) {
     $out .= $in;
     break;
     }
    $chunk_hex = trim(substr($in, 0, $lf_pos));
    $sc_pos = strpos($chunk_hex, ';');
    if($sc_pos !== false)
     $chunk_hex = substr($chunk_hex, 0, $sc_pos);
    if($chunk_hex == '') {
     $out .= substr($in, 0, $lf_pos);
     $in = substr($in, $lf_pos + 1);
     continue;
     }
    $chunk_len = hexdec($chunk_hex);
    if($chunk_len) {
     $out .= substr($in, $lf_pos + 1, $chunk_len);
     $in = substr($in, $lf_pos + 2 + $chunk_len);
     }
    else {
     $in = '';
     }
    }
  return $out;
}

/**
 * Deprecated - Developers should use the getSymbols function instead of getAvailableSymbols.
 */
public function getAvailableSymbols($flush_cache=false) {
  return $this->getSymbols($flush_cache);
}

public function getSymbols($flush_cache=false) {
  
  if($flush_cache){
   $this->all_symbols = array();
   }
  
  if( !count($this->all_symbols) ){
   $query_string = 'symbols/'. $this->getAppID();
   
   $result = $this->doRequest($query_string);
   if( $result->code >= 200 ){
    $symbols = explode("\n", $result->data);
    
    // remove the top "SYMBOL,TITLE,DECIMALS,BASE_SYMBOL,BASE_TITLE,QUOTE_SYMBOL,QUOTE_TITLE" definition line from CSV
    array_shift($symbols);
    
    $got_error = FALSE;
    foreach( $symbols AS $s ){
      $symbol = explode(',', $s);
      
      if( $symbol[0] == '"Status"' ){
       $got_error = TRUE;
       $this->response['status'] = str_replace('"', '', $symbol[1] );
       }
      elseif( $symbol[0] == '"Error Code"' ){
       $got_error = TRUE;
       $this->response['error_code'] = str_replace('"', '', $symbol[1] );
       }
      elseif( $symbol[0] == '"Error Message"' ){
       $got_error = TRUE;
       $this->response['error_message'] = str_replace('"', '', $symbol[1] );
       }
      elseif( count($symbol) >= 7 ) {
       $this->all_symbols[$symbol[0]] = array(
                                          'symbol'=> $symbol[0],
                                          'title'=> $symbol[1],
                                          'decimals'=> $symbol[2],
                                          'base_symbol'=> $symbol[3],
                                          'base_title'=> $symbol[4],
                                          'quote_symbol'=> $symbol[5],
                                          'quote_title'=> $symbol[6],
                                          );
       }
      
      }
    
    if (!$got_error) {
     $this->response['status'] = "OK";
     }
    ksort($this->all_symbols);
    }
   else{
    $this->response['status'] = 'Error';
    $this->response['error_code'] = $result->code;
    $this->response['error_message'] = $result->error;
    return $this->all_symbols;
    }
   }
  
  return $this->all_symbols;
}




/**
 * Deprecated - Developers should use the getIntervals function instead of getAvailableIntervals.
 */
public function getAvailableIntervals($flush_cache=false) {
  return $this->getIntervals($flush_cache);
}

public function getIntervals($flush_cache=false) {
  
  if($flush_cache){
   $this->all_intervals = array();
   }
  
  if( !count($this->all_intervals) ){
   $query_string = 'intervals/'. $this->getAppID();
   
   $result = $this->doRequest($query_string);
   if( $result->code >= 200 ){
    $intervals = explode("\n", $result->data);
    
    // remove the top "INTERVAL,TITLE" definition line from CSV
    array_shift($intervals);
    
    $got_error = FALSE;
    foreach( $intervals AS $i ){
      $interval = explode(',', $i);
      
      if( $interval[0] == '"Status"' ){
       $got_error = TRUE;
       $this->response['status'] = str_replace('"', '', $interval[1] );
       }
      elseif( $interval[0] == '"Error Code"' ){
       $got_error = TRUE;
       $this->response['error_code'] = str_replace('"', '', $interval[1] );
       }
      elseif( $interval[0] == '"Error Message"' ){
       $got_error = TRUE;
       $this->response['error_message'] = str_replace('"', '', $interval[1] );
       }
      elseif( count($interval) == 2 ) {
       $this->all_intervals[$interval[0]] = $interval[1];
       }
      
      }
    
    if (!$got_error) {
     $this->response['status'] = "OK";
     }
    ksort($this->all_intervals);
    }
   else{
    $this->response['status'] = 'Error';
    $this->response['error_code'] = $result->code;
    $this->response['error_message'] = $result->error;
    return $this->all_intervals;
    }
   }
  
  return $this->all_intervals;
}


public function getToken($timeout=900) {
  
   $query_string = 'token/'. $this->getAppID();
   if( $timeout > 0 ) {
    $query_string .= '/tto-' . $timeout;
    }
    
   $token = FALSE;
   $result = $this->doRequest($query_string);
   if( $result->code >= 200 ) {
    $rows = explode("\n", $result->data);
    
    // remove the top "NAME,VALUE" definition line from CSV
    array_shift($rows);
    
    foreach( $rows AS $r ){
      $info = explode(',', $r);
      
      if( $info[0] == '"Status"' ){
       $this->response['status'] = str_replace('"', '', $info[1] );
       }
      elseif( $info[0] == '"Error Code"' ){
       $this->response['error_code'] = str_replace('"', '', $info[1] );
       }
      elseif( $info[0] == '"Error Message"' ){
       $this->response['error_message'] = str_replace('"', '', $info[1] );
       }
      elseif( $info[0] == '"TOKEN"' ){
       $token = str_replace('"', '', $info[1] );
       }
      }
    }
   else{
    $this->response['status'] = 'Error';
    $this->response['error_code'] = $result->code;
    $this->response['error_message'] = $result->error;
    return $this->all_intervals;
    }
  
  return $token;
}



public function getConversion($convert_from, $convert_to, $convert_value) {
  
  $query_string = 'convert/'. $this->getAppID();
  $query_string .= '/' . $convert_value;
  $query_string .= '/' . $convert_from;
  $query_string .= '/' . $convert_to;
  
  $result = $this->doRequest($query_string);
  
  $conversion = FALSE;
  if( $result->code >= 200 ){
    $rows = explode("\n", $result->data);
    
    $in_data = 0;
    foreach( $rows AS $i=>$r ){
      $row = explode(',', $r );
      
      if( $row[0] == 'QUOTE START' ){
       $in_data = 1;
       }
      elseif( $row[0] == 'QUOTE END' ){
       $in_data = 0;
       }
      elseif( $in_data ){
       /* Conversion Format:
        * SYMBOL,TITLE,FROM_BASE,TO_BASE,AMOUNT,CONVERSION_RATE,CONVERSION_VALUE
        */
       $conversion = array();
       $conversion['symbol'] = $row[0];
       $conversion['title'] = $row[1];
       $conversion['convert_from'] = $row[2];
       $conversion['convert_to'] = $row[3];
       $conversion['convert_value'] = $row[4];
       $conversion['conversion_rate'] = $row[5];
       $conversion['conversion_value'] = $row[6];
       break;
       }
     elseif( $row[0] == '"Status"' ){
      $this->response['status'] = str_replace('"', '', $row[1] );
      }
     elseif( $row[0] == '"Error Code"' ){
      $this->response['error_code'] = str_replace('"', '', $row[1] );
      }
     elseif( $row[0] == '"Error Message"' ){
      $this->response['error_message'] = str_replace('"', '', $row[1] );
      }
    }
  }
  else{
   $this->response['status'] = 'Error';
   $this->response['error_code'] = $result->code;
   $this->response['error_message'] = $result->error;
   return $conversion;
   }
  
  return $conversion;
}


public function getDataCached() {
  return $this->quotes;
}

public function getData() {
  
  $this->cache = '';
  $this->quotes = array();
  $this->iteratorReset();
  
  $price = $this->getPrice();
  $fields = $this->getFields();
  
  $query_string = 'data/'. $this->getAppID();
  if( $this->getStartTime() > 1 ){
   $query_string .= '/st-' . $this->getStartTime();
   }
  $query_string .= '/s-' . $this->getSymbol();
  $query_string .= '/n-' . $this->getPeriods();
  $query_string .= '/i-' . $this->getInterval();
  $query_string .= '/p-' . $price;
  $query_string .= '/f-csv';
  // $query_string .= '/sep-,';
  
  if( $fields == 'ohlcv' ){
   $query_string .= '/fd-' . $fields;
   }
  
  $result = $this->doRequest($query_string);
  if( $result->code >= 200 ){
   $this->cache = $result->data;
   }
  else{
   $this->response['status'] = 'Error';
   $this->response['error_code'] = $result->code;
   $this->response['error_message'] = $result->error;
   return $this->quotes;
   }
  
  $counter = 0;
  if( $this->cache && $this->cache != '' ) {
   
   $quotes = explode("\n", $this->cache);
   $in_data = 0;
   foreach( $quotes AS $i=>$qt ){
     $quote = explode(',', $qt );
     
     if( $quote[0] == 'QUOTE START' ){
      $in_data = 1;
      }
     elseif( $quote[0] == 'QUOTE END' ){
      $in_data = 0;
      }
     elseif( $in_data ){
      
      $q = array();
      
      /* Tick-data
       * Format: <SYMBOL>;<TITLE>;<TIMESTAMP>;<MID,BID>;<ASK>
       */
      if( $this->getInterval() == 1 && $quote[3] ) {
       $q['symbol']    = $quote[0];
       $q['title']     = $quote[1];
       $q['time']      = $quote[2];
       
       if( $price == 'bid' ) {
        $q['bid']      = $quote[3];
        }
       elseif( $price == 'ask' ){
        $q['ask']      = $quote[3];
        }
       elseif( $price == 'mid' ){
        $q['mid']      = $quote[3];
        }
       elseif( $price == 'bid,ask' && $quote[4] ){
        $q['bid']      = $quote[3];
        $q['ask']      = $quote[4];
        }
       elseif( $price == 'bid,ask,mid' && $quote[5] ){
        $q['bid']      = $quote[3];
        $q['ask']      = $quote[4];
        $q['mid']      = $quote[5];
        }
       }
      
      /* OHLC data
       * Format: <SYMBOL>;<TITLE>;<TIMESTAMP>;<OPEN>;<HIGH>;<LOW>;<CLOSE>
       */
      elseif( count($quote) >= 7 ) {
       $q['symbol']    = $quote[0];
       $q['title']     = $quote[1];
       $q['time']      = $quote[2];
       $q['open']      = $quote[3];
       $q['high']      = $quote[4];
       $q['low']       = $quote[5];
       $q['close']     = $quote[6];
       $q['volume']    = (count($quote) >= 8) ? $quote[7] : 0;
       }
      ;
      
      if( count($q) ){
       $this->quotes[] = $q;
       if($this->debug) print "PUSH DATA: '". $qt ."'<br>\n";
       }
      $counter++;
      }
     
     elseif( $quote[0] == '"Status"' ){
      $this->response['status'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Error Code"' ){
      $this->response['error_code'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Error Message"' ){
      $this->response['error_message'] = str_replace('"', '', $quote[1] );
      }
     
     /* ------------------------------------ */
     elseif( $quote[0] == '"Version"' ){
      $this->response['api_version'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Copyright"' ){
      $this->response['copyright'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Website"' ){
      $this->response['website'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Redistribution"' ){
      $this->response['redistribution'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"License"' ){
      $this->response['licence'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Access Period"' ){
      $this->response['access_period'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Access Limit"' ){
      $this->response['max_access'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Access Count"' ){
      $this->response['access_count'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Access Remaining"' ){
      $this->response['access_remaining'] = str_replace('"', '', $quote[1] );
      }
     elseif( $quote[0] == '"Period Start"' ){
      $this->response['access_period_began'] = str_replace('"', '', $quote[1] ). " " .
                                   str_replace('"', '', $quote[2] ); /* Timestamp Timezone */
      }
     elseif( $quote[0] == '"Next Period"' ){
      $this->response['access_period_starts'] = str_replace('"', '', $quote[1] ). " " .
                                    str_replace('"', '', $quote[2] ); /* Timestamp Timezone */
      }
     
     }
   }
  
//   return $counter;
  return $this->quotes;
}



 /* ---------------------------------------------------
  * Info methods
  * ---------------------------------------------------
  */
public function getNumQuotes(){
  $cnt = count($this->quotes);
  return ($cnt) ? $cnt : 0;
}

public function getStatus(){
  return $this->response['status'];
}
public function getErrorCode(){
  return $this->response['error_code'];
}
public function getErrorMessage(){
  return $this->response['error_message'];
}

public function getAPIVersion(){
  return $this->response['api_version'];
}
public function getCopyright(){
  return $this->response['copyright'];
}
public function getWebsite(){
  return $this->response['website'];
}
public function getRedistribution(){
  return $this->response['redistribution'];
}
public function getLicense(){
  return $this->response['licence'];
}
public function getLicenseInfo(){
  return $this->getLicense() ." ". $this->getRedistribution();
}


public function getAccessPeriod(){
  return $this->response['access_period'];
}
public function getAccessPerPeriod(){
  return $this->response['max_access'];
}
public function getAccessThisPeriod(){
  return $this->response['access_count'];
}
public function getAccessPeriodRemaining(){
  return $this->response['access_remaining'];
}
public function getAccessPeriodBegan(){
  return $this->response['access_period_began'];
}
public function getAccessPeriodStarts(){
  return $this->response['access_period_starts'];
}




 /* ---------------------------------------------------
  * Iterator methods
  * ---------------------------------------------------
  */

/* Reset the iterator.
 * @param $pos - optionally set the index position of the iterator
 */
public function iteratorReset($pos=0){
  if( !$pos ) $pos = 0;
  $pos--;  /* Allow a 1 based value on a 0 based array index. */
  $this->pos = $pos;
  return $pos;
}

/**
 * Increments the iterator by one.
 * @return current position or 0 if invalid
 */
public function iterator(){
  $this->pos++;
  return ($this->pos >= $this->getNumQuotes() ) ? 0 : 1+$this->pos;
}


/**
 * Get the %quote hash from the currenct Iterator position
 */
public function iteratorGetQuote(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos];
   }
  return 0;
}

/**
 * Get the High value at the currenct Iterator position
 */
public function iteratorGetSymbol(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['symbol'];
   }
  return 0;
}
/**
 * Get the High value at the currenct Iterator position
 */
public function iteratorGetTitle(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['title'];
   }
  return 0;
}
/**
 * Get the High value at the currenct Iterator position
 */
public function iteratorGetTimestamp() {
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['time'];
   }
  return 0;
}


/**
 * Get the High value at the currenct Iterator position
 */
public function iteratorGetOpen(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['open'];
   }
  return 0;
}

/**
 * Get the High value at the currenct Iterator position
 */
public function iteratorGetHigh(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['high'];
   }
  return 0;
}

/**
 * Get the Low value at the currenct Iterator position
 */
public function iteratorGetLow(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['low'];
   }
  return 0;
}

/**
 * Get the Close value at the currenct Iterator position
 */
public function iteratorGetClose(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['close'];
   }
  return 0;
}

/**
 * Get the Volume value at the currenct Iterator position
 */
public function iteratorGetVolume(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['volume'];
   }
  return 0;
}

/**
 * Get the Price value at the currenct Iterator position (only for tick-data)
 */
public function iteratorGetPrice(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['price'];
   }
  return 0;
}
/**
 * Get the Bid value at the currenct Iterator position (only for tick-data)
 */
public function iteratorGetBid(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['bid'];
   }
  return 0;
}
/**
 * Get the Ask value at the currenct Iterator position (only for tick-data)
 */
public function iteratorGetAsk(){
  if($this->pos >= 0 && $this->pos < $this->getNumQuotes() ){
   return $this->quotes[$this->pos]['ask'];
   }
  return 0;
}




}

