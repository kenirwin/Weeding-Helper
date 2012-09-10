<?
/********************************************************************
#
#  sortLC.php : 2 PHP functions to sort Library of Congress (LC) call numbers
#
#  Version: 0.1 alpha 
#
#  Ported and adapted for PHP by Ken Irwin, kirwin@wittenberg.edu
#  Based on sortLC.pl (version 1.2) by Michael Doran, doran@uta.edu
#        University of Texas at Arlington Libraries
#        Box 19497, Arlington, TX 76019, USA
#
#
#  Usage:
#    This file contains two functions: SortLC() and NormalizeLC()
#    SortLC() uses NormalizeLC(). To make use of this functionality from
#    within a PHP script, first establish an array of call numbers 
#    or an associative array in which the call number is the key.
#   
#    For a simple array, use the usort() function to sort by call number, e.g.:
#      
#      usort($array, "SortLC");
#
#    For an associative array, use the uksort() function to sort, e.g.:
# 
#      uksort($array, "SortLC");
#    
#
#    Unparsable call numbers are sorted to the beginning of the array.
#
#    Identical call numbers are NOT deduped, although some de-duplication
#    can be accomplished by using the array_unique() function in the main
#    script from which these functions are called, however this only dedups 
#    exactly identical call number strings. If two call numbers are 
#    functionally the same but are differently-spaced or punctuated, they 
#    will be treated as two separate call numbers by the array_unique()
#    function.
#
#
#  Disclaimer: This application relies on an LC call number
#              normalization routine for sorting. The normalization
#              routine is far from perfect, therefore the sorted
#              output will also be imperfect.
#
#  Known flaws: 
#        * Except for copy, vol, etc numbers, sorting may break down after 
#          second set of cutter numbers.
#
////////////////////////////////////////////////////////////////////////
#
#  Significant differences between original Perl script and this PHP version:
#        * This version attempts to sort some of the post-cutter information,
#          including issue, volume, part, binding, and disc numbers.
#          This feature will hopefully appear in future versions of sortLC.pl
#        * This PHP version is intended for use with PHP's user-defined 
#          sort commands: usort() and uksort(), whereas sortLC.pl is
#          usable as a standalone script for sort a file full of call numbers.
#          See "Usage" section above.
#          
#
///////////////////////////////////////////////////////////////////
#  The original Perl script upon which this port is based bears this 
#  copyright notice. It applies to most of the code in this port as well:
#
#       Copyright 2005, The University of Texas at Arlington ("UTA").
#       All rights reserved.
#     
#       By using this software the USER indicates that he or she 
#       has read, understood and and will comply with the following:
#
#       UTA hereby grants USER permission to use, copy, modify, and
#       distribute this software and its documentation for any 
#       purpose and without fee, provided that:
#
#       1. the above copyright notice appears in all copies of the
#       software and its documentation, or portions thereof, and
#
#       2. a full copy of this notice is included with the software 
#       and its documentation, or portions thereof, and
#
#       3. neither the software nor its documentation, nor portions
#       thereof, is sold for profit.  Any commercial sale or license
#       of this software, copies of the software, its associated
#       documentation and/or modifications of either is strictly
#       prohibited without the prior consent of UTA.
#
#       Title to copyright to this software and its associated
#       documentation shall at all times remain with UTA.  No right
#       is granted to use in advertising, publicity or otherwise any
#       trademark, service mark, or the name of UTA.
#
#       This software and any associated documentation are provided
#       "as is," and UTA MAKES NO REPRESENTATIONS OR WARRANTIES,
#       EXPRESSED OR IMPLIED, INCLUDING THOSE OF MERCHANTABILITY OR
#       FITNESS FOR A PARTICULAR PURPOSE, OR THAT USE OF THE SOFTWARE,
#       MODIFICATIONS, OR ASSOCIATED DOCUMENTATION WILL NOT INFRINGE
#       ANY PATENTS, COPYRIGHTS, TRADEMARKS OR OTHER INTELLECTUAL
#       PROPERTY RIGHTS OF A THIRD PARTY. UTA, The University of Texas
#       System, its Regents, officers, and employees shall not be
#       liable under any circumstances for any direct, indirect, special,
#       incidental, or consequential damages with respect to any claim
#       by USER or any third party on account of or arising from the
#       use, or inability to use, this software or its associated
#       documentation, even if UTA has been advised of the possibility
#       of those damages.
#
#       Submit commercialization requests to: The University of Texas
#       at Arlington, Office of Grant and Contract Services, 701 South
#       Nedderman Drive, Box 19145, Arlington, Texas 76019-0145,
#       ATTN: Director of Technology Transfer.
#
********************************************************************/


/*********************************************************************
 * SortLC
 *********************************************************************/

function SortLC ($right, $left) {
  $right = NormalizeLC($right);
  $left = NormalizeLC($left);
  return (strcmp($right,$left));
} // end SortLC


/*********************************************************************
 *  NormalizeLC
 *********************************************************************/

function NormalizeLC ($lc_call_no_orig) {
  /*
    User defined setting: set problems to top to sort unparsable 
    call numbers to the top of the list; false to sort them to the 
    bottom.
  */
  $problems_to_top = "true";
  if ($problems_to_top == "true") { 
    $unparsable = " ";
  }
  else { $unparsable = "~"; } 

  //Convert all alpha to uppercase
  $lc_call_no = strtoupper($lc_call_no_orig);


  // define special trimmings that indicate integer
  $integer_markers = array("C.","BD.","DISC","DISK","NO.","PT.","V.","VOL.");
  foreach ($integer_markers as $mark) {
    $mark = str_replace(".", "\.", $mark);
    $lc_call_no = preg_replace("/$mark(\d+)/","$mark$1;",$lc_call_no);
  } // end foreach int marker

  // Remove any inital white space
  $lc_call_no = preg_replace ("/\s*/","",$lc_call_no);

  
  if (preg_match("/^([A-Z]{1,3})\s*(\d+)\s*\.*(\d*)\s*\.*\s*([A-Z]*)(\d*)\s*([A-Z]*)(\d*)\s*(.*)$/",$lc_call_no,$m)) {
    $initial_letters = $m[1];
    $class_number    = $m[2];
    $decimal_number  = $m[3];
    $cutter_1_letter = $m[4];
    $cutter_1_number = $m[5];
    $cutter_2_letter = $m[6];
    $cutter_2_number = $m[7];
    $the_trimmings   = $m[8];
  } //end if call number match

  else { return ($unparsable); } // return extreme answer if not a call number

  if ($cutter_2_letter && ! ($cutter_2_number)) {
    $the_trimmings = $cutter_2_letter . $the_trimmings;
    $cutter_2_letter = '';
  }

  /* TESTING NEW SECTION TO HANDLE VOLUME & PART NUMBERS */

  foreach ($integer_markers as $mark) {
    if (preg_match("/(.*)($mark)(\d+)(.*)/", $the_trimmings, $m))  {
      $trim_start = $m[1];
      $int_mark = $m[2];
      $int_no = $m[3];
      $trim_rest = $m[4];
      $int_no = sprintf("%5s", $int_no);
      $the_trimmings = $trim_start . $int_mark . $int_no . $trim_rest;
    } // end if markers in the trimmings
  } // end foreach integer marker
  /* END NEW SECTION */


  if ($class_number) {
    $class_number = sprintf("%5s", $class_number);
  }
  $decimal_number = sprintf("%-12s", $decimal_number);
  if ($cutter_1_number) {
    $cutter_1_number = " $cutter_1_number";
  }
  if ($cutter_2_letter) {
    $cutter_2_letter = "   $cutter_2_letter";
  }
  if ($cutter_2_number) {
    $cutter_2_number = " $cutter_2_number";
  }
  if ($the_trimmings) {
    $the_trimmings = preg_replace ("/(\.)(\d)/","$1 $2", $the_trimmings);
    $the_trimmings = preg_replace ("/(\d)\s*-\s*(\d)/","$1-$2", $the_trimmings);
    //    $the_trimmings =~ s/(\d+)/sprintf("%5s", $1)/ge;
    $the_trimmings = "   $the_trimmings";
  }
  $normalized = "$initial_letters" . "$class_number"
    . "$decimal_number"  . "$cutter_1_letter"
    . "$cutter_1_number" . "$cutter_2_letter"
    . "$cutter_2_number" . "$the_trimmings";
  
  return("$normalized");
} // end NormalizeLC




?>


