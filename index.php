<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/misc.php';

    /*****************************************************
     * 
     * 
     * System-level checks
     * 
     * 
    ******************************************************/

    // MongoDB Extension check, Method #1
    // you can disable extension and restart your webserver to make this fail
    //
    if ( extension_loaded('mongodb') ) {
        echo(MSG_EXTENSION_LOADED_SUCCESS);
    } else {
            echo(MSG_EXTENSION_LOADED_FAIL);
    }

    // MongoDB Extension check, Method #2
    if ( !class_exists('MongoDB\Driver\Manager') ) {
        echo(MSG_EXTENSION_LOADED2_FAIL); 
        exit();
    } 
    else {
        echo(MSG_EXTENSION_LOADED2_SUCCESS);
    }

    // MongoDB Extension check, Method #3
    $libversion = phpversion('mongodb');
    if ( $libversion ) {
        echo("version $libversion".MSG_EXTENSION_LOADED3_SUCCESS);
    } else {
        echo(MSG_EXTENSION_LOADED3_FAIL);
    }

    // MongoDB PHP Library check
    if ( !class_exists('MongoDB\Client')  ) {
        echo(MSG_LIBRARY_MISSING); 
        exit();
    } 
    else {
        echo(MSG_LIBRARY_PRESENT);
    }

    /*****************************************************
     * 
     * 
     * MongoDB Client and credentials/auth check 
     * 
     * 
    ******************************************************/

    // checks if you have an .env file with your credentials. 
    if ( !file_exists('.env') ) {
        echo(MSG_ENV_FAIL);
        exit();
    }

    // using phpdotenv to store our credentials in an .env file 
    // https://github.com/vlucas/phpdotenv
    //
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Fail if the MongoDB Extension is not configuired and loaded
    // Fail if the connection URL is wrong
    try {
        // IMPORTANT: replace with YOUR server DNS name
        $mdbserver = 'serverlessinstance0.owdak.mongodb.net';

        $client = new MongoDB\Client('mongodb+srv://'.$_ENV['MDB_USER'].':'.$_ENV['MDB_PASS'].'@'.$mdbserver.'/?retryWrites=true&w=majority');
        echo(MSG_CLIENT_SUCCESS);
        // succeeds even if user/password is invalid
    }
    catch (\Exception $e) {
        // fails if the URL is malformed
        // fails without a succesful network access to cluster
        //      fails if the IP is blocked by an ACL or firewall
        echo(MSG_CLIENT_FAIL);
        exit();
    }


    try { 
        // if listDatabaseNames() works, your authorization is valid
        $databases_list_iterator = $client->listDatabaseNames(); // asks for a list of database names on the cluster

        $databases_list          = iterator_to_array( $databases_list_iterator );
        echo( MSG_CLIENT_AUTH_SUCCESS );
    }
    catch (\Exception $e) {
        // Fail if incorrect user/password, or not authorized
        // Could be another issue, check content of $e->getMessage()
        echo( MSG_EXCEPTION. $e->getMessage() );
        exit();
    }

    // check if our desired database is present in the cluster by looking up its name
    $workingdbname = 'sample_analytics';
    if ( in_array( $workingdbname, $databases_list ) ) {
        echo( MSG_DATABASE_FOUND." '$workingdbname'<br>"  );
    }
    else {
        echo( MSG_DATABASE_NOT_FOUND." '$workingdbname'<br>"  );
        exit();
    }

    // check if your desired collection is present in the database
    $workingCollectionname      = 'customers';
    $collections_list_itrerator = $client->$workingdbname->listCollections();
    $foundCollection            = false;
    
    $collections_list_itrerator->rewind();
    while( $collections_list_itrerator->valid() ) {
        if ( $collections_list_itrerator->current()->getName() == $workingCollectionname ) {
            $foundCollection = true;
            echo( MSG_COLLECTION_FOUND." '$workingCollectionname'<br>"  );
            break; 
        }
        $collections_list_itrerator->next();
    }

    if ( !$foundCollection ) {
        echo( MSG_COLLECTION_NOT_FOUND." '$workingCollectionname'<br>"  );
        exit();
    }

    /*****************************************************
     * 
     * 
     * CRUD Operation Error Handling 
     * 
     * 
    ******************************************************/

    // we'll work on this collection
    $customers   = $client->selectCollection($workingdbname, $workingCollectionname );

    /*****************************************************
     * Create
    ******************************************************/

    $fixedid = new MongoDB\BSON\ObjectId("5a2493c33c95a1281836eb6a");
    
    // uncomment the insertOne below to create a duplicate key exception during insertOne()
    //
    //$customers->insertOne( ['_id'=> $fixedid, 'username' => 'newuser_withfixedid'] );

    try {
        $result    = $customers->insertOne( ['_id'=> $fixedid, 'username' => 'newuser_withfixedid'] );
        $insertedcount = $result->getInsertedCount(); // $insertedcount == 1 
        if ($insertedcount == 1) {
            echo( MSG_INSERTONE_SUCCESS );
        }
    }
    catch (\Exception $e) {       
        // example: fails if trying adding document with a duplicate ID
        echo( MSG_INSERTONE_FAIL. $e->getMessage() );
    }

   /*****************************************************
     * Read
    ******************************************************/

    // querying a document we know exists.  
    // the returned $document should contain a non-null, valid document
    $document = $customers->findOne(['username' => 'wesley20']); 
    if ($document->username == "wesley20") {
        echo( MSG_FINDONE_SUCCESS );
    }

    // querying a document which does not exist. $document will be null
    $document = $customers->findOne(['username' => 'thisuserdoesnotexist']);


    /*****************************************************
     * ❌ Update
    ******************************************************/
    // ❌ add an update exemple that triggers an exception


    /*****************************************************
     * Delete the record we previously added
    ******************************************************/
    try {
        $document   = $customers->deleteOne( ['_id'=> $fixedid] );
        $numdeleted = $document->getDeletedCount(); // $numdeleted should be 1 
        echo ( MSG_DELETEONE_SUCCESS );
    }
    catch (\Exception $e) {
        echo($e->getMessage() );
    }

    // trying to delete a document which does not exist. 
    // Won't cause an exception, but no document is modified
    try {
        $document   = $customers->deleteOne( ['username' => 'thisuserdoesnotexist'] );
        $numdeleted = $document->getDeletedCount(); // $numdeleted should be 0 
    }
    catch (\Exception $e) {
        echo($e->getMessage() );
    }
?>