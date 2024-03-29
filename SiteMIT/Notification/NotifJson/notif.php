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

        /// nombre non remis
// select count(*) from presence_pc_portable as t1 where t1.status = 'Retrait' and not exists(select id_pc_portable from presence_pc_portable as t2
// where t2.status = 'Remise' and t2.id_pc_portable = t1.id_pc_portable and t2.date_operation > t1.date_operation);

// /// non remis affichage
// select personnes.prenoms,t1.id_pc_portable,inscription.grade,inscription.niveau,presence.date_presence as presence,t1.date_operation as retrait,t3.date_operation as remise from presence_pc_portable as t1
//                 left join machine_etudiants on
//                     machine_etudiants.id_pc_etudiant = t1.id_pc_portable
//                 left join inscription on
//                     inscription.id_etudiant = machine_etudiants.id_inscription_etudiant
//                 left join etudiants on
//                     etudiants.id_etudiant = inscription.id_etudiant
//                 left join personnes on
//                     personnes.id_personne = etudiants.id_personne
//                 left join presence on
//                     presence.id_etudiant = etudiants.id_etudiant
//                     and DATE(presence.date_presence) = DATE(t1.date_operation) 
//                 left join presence_pc_portable as t3 on 
//                     t3.id_pc_portable=t1.id_pc_portable 
//                     and t3.date_operation>t1.date_operation 
//                     and t3.status = 'Remise' 
//                 where t1.status = 'Retrait' 
//                     and not exists 
//                         (select id_pc_portable from presence_pc_portable as t2 
//                             where t2.status = 'Remise' and 
//                             t2.id_pc_portable = t1.id_pc_portable and 
//                             t2.date_operation > t1.date_operation); 
         

?>

