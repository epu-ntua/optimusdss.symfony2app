arc2-sparql11
=============

arc2-sparql11 provides ARC2 (<http://arc.semsol.org/>) with *some support* for SPARQL 1.1 remote stores and endpoints.

At present, SPARQL 1.1 SELECT, INSERT, DELETE queries are supported.

Documentation
-------------

An ARC2_SPARQL11RemoteStore is instantiated as follows:

    include_once('path/to/arc/ARC2.php');

    $config = array( 'remote_store_endpoint' => 'http://example.com/sparql' );
    $store = ARC2::getComponent('SPARQL11RemoteStore', $config);

See the [ARC2 Remote Stores and Endpoints](https://github.com/semsol/arc2/wiki/Remote-Stores-and-Endpoints) documentation for details on using ARC2 remote stores.

ARC2
----

>ARC2 is a PHP 5.3 library for working with RDF. It also provides a MySQL-based triplestore with SPARQL support.
>
>Feature-wise, ARC2 is now in a stable state with no further feature additions planned. Issues are still being fixed and Pull Requests are welcome, though.

The current ARC2 does not support parsing SPARQL 1.1 queries. arc2-sparql11 works by providing an additional ARC2_RemoteStore implementation (ARC2_SPARQL11RemoteStore) which avoids most parsing most of the SPARQL query locally, allowing it to be passed to the remote store and parsed remotely.
