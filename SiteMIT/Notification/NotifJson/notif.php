<?php
    $yesterday = date('Y-m-d',time()-24*3600);
    $today = date('Y-m-d');

    $connexion = mysqli_connect("localhost","mit","123456","mit")
    or die("Echec de connexion");


    /// nombre pc non pris
    $query = "select count(*) from machine_etudiants 
                where id_pc_etudiant not in 
                    (select id_pc_portable from presence_pc_portable 
                        where status='Retrait' 
                        and date_operation > '{$yesterday}' 
                        and date_operation < '{$today}')";

    $result = mysqli_query($connexion,$query);
    $table = mysqli_fetch_assoc($result);
    $count = $table['count(*)'];
    $list = ["count_pc_non_pris" => $count];

    /// nombre pc non remis
    $query = "select count(*) from presence_pc_portable as t1 
                where t1.status = 'Retrait' and not exists 
                    (select id_pc_portable from presence_pc_portable as t2
                        where t2.status = 'Remise' 
                        and t2.id_pc_portable = t1.id_pc_portable 
                        and t2.date_operation > t1.date_operation);";

    $result = mysqli_query($connexion,$query);
    $table = mysqli_fetch_assoc($result);
    $count = $table['count(*)'];
    $list["count_pc_non_remis"] = $count;

    /// nombre des absents
    $query = "select count(*) from inscription 
                where id_inscription not in 
                    ( select id_etudiant from presence 
                        where statut = 1 
                        and date_presence like '{$yesterday}%')";

    $result = mysqli_query($connexion,$query);
    $table = mysqli_fetch_assoc($result);
    $count = $table['count(*)'];
    $list["count_absent"] = $count;


    mysqli_close($connexion);
    $json = json_encode($list);
    print_r($list);

?>

