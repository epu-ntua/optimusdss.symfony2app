<?php
/**
 * Not really a SPARQL Parser for SPARQL 1.1.
 *
 * The parser should return the required data for ARC2_RemoteStore::query,
 * without having to parse all SPARQL 1.1 language features.
 *
 * This class is really a slightly hacked version of ARC2_SPARQLPlusParser 
 * that skips most of the parsing so that the query can be handed to a 
 * remote SPARQL 1.1 store.
 *
 * @author Stuart Taylor <staylor@abdn.ac.uk>
 * @package ARC2
 * @version 2012-03-22
*/

ARC2::inc('SPARQLPlusParser');

class ARC2_SPARQL11MiniParser extends ARC2_SPARQLPlusParser {

  function __construct($a, &$caller) {
    parent::__construct($a, $caller);
  }

  function __init() {
    parent::__init();
  }

  // Override - only support compatible query types.
  function xQuery($v) {
    list($r, $v) = $this->xPrologue($v);
    //foreach (array('Select', 'Construct', 'Describe', 'Ask', 'Insert', 'Delete', 'Load') as $type) {
    foreach (array('Select', 'Insert', 'Delete') as $type) {
      $m = 'x' . $type . 'Query';
      if ((list($r, $v) = $this->$m($v)) && $r) {
        return array($r, $v);
      }
    }
    return array(0, $v);
  }

  // Override - don't parse group graph patterns.
  function xGroupGraphPattern($v) {
    return array(array('type' => 'group', 'patterns' => array()), '');
  }

  // Override - support SPARQL 1.1 style INSERT DATA queries.
  function xInsertQuery($v) {
    if ($sub_r = $this->x('INSERT\s+', $v)) {
      return array(array('type' => 'insert', 'dataset' => array()), '');
    }
    
    return array(0, $v);
  }

  // Override - support SPARQL 1.1 style DELETE queries.
  function xDeleteQuery($v) {
    if ($sub_r = $this->x('DELETE\s+', $v)) {
      return array(array('type' => 'delete', 'dataset' => array()), '');
    }
    
    return array(0, $v);
  }

}
