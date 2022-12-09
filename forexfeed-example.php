<?php
/**
 * ForexFeed.Net Data API
 *
 * Copyright 2009 ForexFeed.Net <copyright@forexfeed.net>
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
 */
 
 // Load the ForexFeed.net API
 require_once('ForexFeed.class.php');



  /* ------------------------------------------
   * EDIT THE FOLLOWING VARIABLES
   */
  
  /**
   * 
   * NOTE: You must replace "YOUR_APP_ID" below with your unique 15-character AppID
   *       which can  be received by logging into your account on our website at
   *       http://forexfeed.net
   * 
   */
    
  $app_id = 'YOUR_APP_ID';
  $symbol = 'EURUSD,GBPUSD,USDCHF,USDCAD,AUDUSD';
  $interval = 3600;
  $periods = 1;
  
  /* END VARIABLES
   * ------------------------------------------
   */
  
  
  
  
  
  
  /*  Create the ForexFeed Object
   */
  $fxfeed = new ForexFeed(
           array(
             'app_id' => $app_id,
             'symbol'     => $symbol,
             'interval'   => $interval,
             'periods'    => $periods,
             )
            );
  
  // Display a Conversion
  printConversion($fxfeed);

// Display the data
  printData($fxfeed);
  
  // Display the available Intervals
  printIntervals($fxfeed);
  
  // Display the available Symbols
  printSymbols($fxfeed);


/**
 *  Get a conversion and print it to System.out
 */
function printConversion($fxfeed) {

  /*  Get the conversion */
  $conversion = $fxfeed->getConversion("EUR", "USD", 1);
//   $conversion = $fxfeed->getConversion("USD", "EUR", 1);

  print("<br>\r\n-------- Conversion --------<br>\r\n");
  if( $fxfeed->getStatus() == "OK" ){
   print $conversion["convert_value"] . " ";
   print $conversion["convert_from"] . " = ";
   print $conversion["conversion_value"] . " ";
   print $conversion["convert_to"] . " ";
   print "(rate: " . $conversion["conversion_rate"] . ") <br>\r\n";
   print "<br>\r\n";
   }
  else {
   print "Status: " . $fxfeed->getStatus(). "<br>\r\n";
   print "ErrorCode: " . $fxfeed->getErrorCode(). "<br>\r\n";
   print "ErrorMessage: " . $fxfeed->getErrorMessage(). "<br>\r\n";
   }
}



/**
 *  Get the data and print it to screen,
 */
function printData($fxfeed) {
  
  /*  Request the Data  */
  $fxfeed->getData();
  
  print "<br>\r\n-------- Quotes --------<br>\r\n";
  if( $fxfeed->getStatus() == "OK" ){
   print "Number of Quotes: " . $fxfeed->getNumQuotes() . "<br><br>\r\n";
   print "Copyright: " . $fxfeed->getCopyright() . "<br>\r\n";
   print "Website: " . $fxfeed->getWebsite() . "<br>\r\n";
   print "License: " . $fxfeed->getLicense() . "<br>\r\n";
   print "Redistribution: " . $fxfeed->getRedistribution() . "<br>\r\n";
   print "AccessPeriod: " . $fxfeed->getAccessPeriod() . "<br>\r\n";
   print "AccessPerPeriod: " . $fxfeed->getAccessPerPeriod() . "<br>\r\n";
   print "AccessThisPeriod: " . $fxfeed->getAccessThisPeriod() . "<br>\r\n";
   print "AccessRemainingThisPeriod: " . $fxfeed->getAccessPeriodRemaining() . "<br>\r\n";
   print "AccessPeriodBegan: " . $fxfeed->getAccessPeriodBegan() . "<br>\r\n";
   print "NextAccessPeriodStarts: " . $fxfeed->getAccessPeriodStarts() . "<br>\r\n";
   print "<br>\r\n";
  
   while( $fxfeed->iterator() ){
     
     print " Quote Symbol: " . $fxfeed->iteratorGetSymbol();
     print " Title: " . $fxfeed->iteratorGetTitle();
     print " Time: " . $fxfeed->iteratorGetTimestamp();
     
     if( $fxfeed->getInterval() == 1 ){
      print " Price: " . $fxfeed->iteratorGetPrice();
      }
     else{
      print " Open: " . $fxfeed->iteratorGetOpen();
      print " High: " . $fxfeed->iteratorGetHigh();
      print " Low: " . $fxfeed->iteratorGetLow();
      print " Close: " . $fxfeed->iteratorGetClose();
      }
     print "<br>\r\n";
     
     }
   }
  else{
   print "Status: " . $fxfeed->getStatus() . "<br>\r\n";
   print "ErrorCode: " . $fxfeed->getErrorCode() . "<br>\r\n";
   print "ErrorMessage: " . $fxfeed->getErrorMessage() . "<br>\r\n";
   }
  
}



/**
 *  Print available Intervals to screen,
 */
function printIntervals($fxfeed) {
  
  /*  Get the Intervals */
  $intervals = $fxfeed->getAvailableIntervals();
  
  print "<br>\r\n-------- Intervals --------<br>\r\n";
  if( $fxfeed->getStatus() == "OK" ){
   foreach( $intervals AS $interval=>$name ){
     print " Interval: " . $interval;
     print " Title: " . $name . "<br>\r\n";
     }
   }
  else {
   print "Status: " . $fxfeed->getStatus(). "<br>\r\n";
   print "ErrorCode: " . $fxfeed->getErrorCode(). "<br>\r\n";
   print "ErrorMessage: " . $fxfeed->getErrorMessage(). "<br>\r\n";
   }
}

/**
 *  Print available Symbols to screen,
 */
function printSymbols($fxfeed) {
  
  /*  Get the Symbols */
  $symbols = $fxfeed->getAvailableSymbols();
  
  print "<br>\r\n-------- Symbols --------<br>\r\n";
  if( $fxfeed->getStatus() == "OK" ){
   foreach( $symbols AS $symbol=>$info ){
     print " Symbol: " . $info['symbol'];
     print " Title: " . $info['title'];
     print " Precision: " . $info['decimals'] . "<br>\r\n";
     }
   }
  else {
   print "Status: " . $fxfeed->getStatus(). "<br>\r\n";
   print "ErrorCode: " . $fxfeed->getErrorCode(). "<br>\r\n";
   print "ErrorMessage: " . $fxfeed->getErrorMessage(). "<br>\r\n";
   }
}



