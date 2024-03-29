<?php
    $connexion = pg_connect("host=localhost dbname=mit user=postgres password=123456")
        or die('Connexion impossible : ' . pg_last_error());

    $query = 'SELECT * FROM inscri';
    $result = pg_query($connexion,$query) or die("Echec de la requête : ".pg_last_error());
    pg_close();


    $all = pg_fetch_all($result);
    $names = [];
    foreach($all as $line){
        $names[] = $line['name'];
    }
    $all = ["presence" => ["id" => 0,"name" => "aro"]];
    print_r($all);
    echo print_r(json_encode($all),true);
?>