<?php

return [
    'zf2-analytics' => [

        // google universal analytics account id (e.g. UA-XXXXXXXX-X)
        'account_id' => 'UA-47467616-2',

        'client_create_random_id' => true, // create a random client id when the class can't fetch the current client id or none is provided by "client_id"
        //'client_fallback_id' => 555, // fallback client id when cid was not found and random client id is off
        //'client_id' => null,    // override client id
        //'user_id' => null,  // determine current user id

        // adapter options
        'adapter' => [
            'async' => true, // requests to google are async - don't wait for google server response
            'ssl' => false // use ssl connection to google server
        ]

        // proxy settings
        //'proxy' => array(
        //   'ip' => '127.0.0.1', // override the proxy ip with this one
        //   'user_agent' => 'override agent' // override the proxy user agent
        //)
    ]
];