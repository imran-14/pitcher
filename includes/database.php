<?php
    require_once 'C:\Users\Ahmed\vendor\autoload.php';

    use GraphAware\Neo4j\Client\ClientBuilder;

    $client = ClientBuilder::create()
        ->addConnection('default','http://neo4j:12345@localhost:7474' ) // Example for HTTP connection configuration (port is optional)
        //->addConnection('bolt', 'bolt://neo4j:password@localhost:7687') // Example for BOLT connection configuration (port is optional)
        ->build();
?>