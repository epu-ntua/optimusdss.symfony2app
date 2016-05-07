<?php
/**
 * ARC2 SPARQL 1.1 compatible Remote RDF Store
 *
 * This class has be modified from ARC2_RemoteStore to allow 
 * ARC2 to query remote SPARQL 1.1 endpoints.
 *
 * @author Stuart Taylor <staylor@abdn.ac.uk>
 * @package ARC2
 * @version 2012-03-22
*/

ARC2::inc('ARC2_RemoteStore');

class ARC2_SPARQL11RemoteStore extends ARC2_RemoteStore {

  function __construct($a, &$caller) {
    parent::__construct($a, $caller);
  }

  function __init() {
    parent::__init();
  }


  function insert($doc, $g, $keep_bnode_ids = 0) {
    // TODO update this to SPARQL 1.1 spec
    return $this->query('INSERT INTO <' . $g . '> { ' . $this->toNTriples($doc, '', 1) . ' }');
  }

  function delete($doc, $g) {
    // TODO update this to SPARQL 1.1 spec

    if (!$doc) {
      return $this->query('DELETE FROM <' . $g . '>');
    }
    else {
      return $this->query('DELETE FROM <' . $g . '> { ' . $this->toNTriples($doc, '', 1) . ' }');
    }
  }

  function replace($doc, $g, $doc_2) {
    // TODO update this to SPARQL 1.1 spec
    return array($this->delete($doc, $g), $this->insert($doc_2, $g));
  }

  /*  */

  function query($q, $result_format = '', $src = '', $keep_bnode_ids = 0, $log_query = 0) {
    if ($log_query) $this->logQuery($q);
    ARC2::inc('SPARQL11MiniParser');
    $p = new ARC2_SPARQL11MiniParser($this->a, $this);
    $p->parse($q, $src);
    $infos = $p->getQueryInfos();
    $t1 = ARC2::mtime();
    if (!$errs = $p->getErrors()) {
      $qt = $infos['query']['type'];
      $r = array('query_type' => $qt, 'result' => $this->runQuery($q, $qt, $infos));
    }
    else {
      $r = array('result' => '');
    }
    $t2 = ARC2::mtime();
    $r['query_time'] = $t2 - $t1;
    /* query result */
    if ($result_format == 'raw') {
      return $r['result'];
    }
    if ($result_format == 'rows') {
      return $this->v('rows', array(), $r['result']);
    }
    if ($result_format == 'row') {
      if (!isset($r['result']['rows'])) return array();
      return $r['result']['rows'] ? $r['result']['rows'][0] : array();
    }
    return $r;
  }

  function runQuery($q, $qt = '', $infos = '') {
    /* ep */
    $ep = $this->v('remote_store_endpoint', 0, $this->a);
    if (!$ep) return false;
    /* prefixes */
    $q = $this->completeQuery($q);
    /* custom handling */
    $mthd = 'run' . $this->camelCase($qt) . 'Query';
    if (method_exists($this, $mthd)) {
      return $this->$mthd($q, $infos);
    }
    /* http verb */
    $mthd = in_array($qt, array('load', 'insert', 'delete')) ? 'POST' : 'GET';
    /* reader */
    ARC2::inc('Reader');
    $reader = new ARC2_Reader($this->a, $this);
    //$reader->setAcceptHeader('Accept: application/sparql-results+xml; q=0.9, application/rdf+xml; q=0.9, */*; q=0.1');
    // Fuseki defaults to JSON with q set.
    //$reader->setAcceptHeader('Accept: application/sparql-results+xml, application/rdf+xml, */*');
    // Now Fuseki needs q set, but nothing after ... (0.2.7 20130416-snapshot)
    $reader->setAcceptHeader('Accept: application/sparql-results+xml; q=0.9');
    if ($mthd == 'GET') {
      $url = $ep;
      $url .= strpos($ep, '?') ? '&' : '?';
      $url .= 'query=' . urlencode($q);
      if ($k = $this->v('store_read_key', '', $this->a)) $url .= '&key=' . urlencode($k);
    }
    else {
      $url = $ep;
      $reader->setHTTPMethod($mthd);
      $reader->setCustomHeaders("Content-Type: application/x-www-form-urlencoded");
      $suffix = ($k = $this->v('store_write_key', '', $this->a)) ? '&key=' . rawurlencode($k) : '';
      $reader->setMessageBody('update=' . rawurlencode($q) . $suffix);
    }
    $to = $this->v('remote_store_timeout', 0, $this->a);
    $reader->activate($url, '', 0, $to);
    $format = $reader->getFormat();
    $resp = '';
    while ($d = $reader->readStream()) {
      $resp .= $this->toUTF8($d);
    }
    $reader->closeStream();
    $ers = $reader->getErrors();
    $this->a['reader_auth_infos'] = $reader->getAuthInfos();
    unset($this->reader);
    if ($ers) return array('errors' => $ers);
    $mappings = array('rdfxml' => 'RDFXML', 'sparqlxml' => 'SPARQLXMLResult', 'turtle' => 'Turtle');
    if (!$format || !isset($mappings[$format])) {
      return $resp;
      // SPARQL update queries return HTML results in Fuseki, which shouldn't be parsed.
      //return $this->addError('No parser available for "' . $format . '" SPARQL result');
    }
    /* format parser */
    $suffix = $mappings[$format] . 'Parser';
    ARC2::inc($suffix);
    $cls = 'ARC2_' . $suffix;
    $parser = new $cls($this->a, $this);
    $parser->parse($ep, $resp);
    /* ask|load|insert|delete */
    if (in_array($qt, array('ask', 'load', 'insert', 'delete'))) {
      $bid = $parser->getBooleanInsertedDeleted();
      if ($qt == 'ask') {
        $r = $bid['boolean'];
      }
      else {
        $r = $bid;
      }
    }
    /* select */
    elseif (($qt == 'select') && !method_exists($parser, 'getRows')) {
      $r = $resp;
    }
    elseif ($qt == 'select') {
      $r = array('rows' => $parser->getRows(), 'variables' => $parser->getVariables());
    }
    /* any other */
    else {
      $r = $parser->getSimpleIndex(0);
    }
    unset($parser);
    return $r;
  }

}
