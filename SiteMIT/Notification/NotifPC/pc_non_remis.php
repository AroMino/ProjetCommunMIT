<?php
    session_start();

    $today = date('Y-m-d');

    if(isset($_GET['search'])) $search = $_GET['search'];
    else $search = $_SESSION['search'];
    $_SESSION['search'] = $search;

    if(isset($_GET['level'])) $level = $_GET['level'];
    else $level = $_SESSION['level'];
    if(empty($level)) $level = "tous";
    $_SESSION['level'] = $level;
    

    /// Requete
    $connexion = mysqli_connect("localhost","mit","123456","mit")
    or die("Echec de connexion");

    $query = "select personnes.prenoms,t1.id_pc_portable as id_pc,inscription.grade,inscription.niveau,t1.date_operation as retrait,presence.date_presence as presence from presence_pc_portable as t1
                left join machine_etudiants on
                    machine_etudiants.id_pc_etudiant = t1.id_pc_portable
                left join inscription on
                    inscription.id_etudiant = machine_etudiants.id_inscription_etudiant
                left join etudiants on
                    etudiants.id_etudiant = inscription.id_etudiant
                left join personnes on
                    personnes.id_personne = etudiants.id_personne
                left join presence on
                    presence.id_etudiant = etudiants.id_etudiant
                    and DATE(presence.date_presence) = DATE(t1.date_operation) 
                left join presence_pc_portable as t3 on 
                    t3.id_pc_portable=t1.id_pc_portable 
                    and t3.date_operation>t1.date_operation 
                    and t3.status = 'Remise' 
                where t1.status = 'Retrait'
                    and t1.date_operation < '{$today}' 
                    and not exists 
                        (select id_pc_portable from presence_pc_portable as t2 
                            where t2.status = 'Remise' and 
                            t2.id_pc_portable = t1.id_pc_portable and 
                            t2.date_operation > t1.date_operation)";

    $result = mysqli_query($connexion,$query);

    $table = [];
    while($line = mysqli_fetch_assoc($result)){
        $line['grade'] = $line['grade'].$line['niveau'];
        unset($line['niveau']);
        
        if($line['grade'] === $level || $level === 'tous'){
            if(strstr(strtolower($line["prenoms"]),strtolower($search))) $table[] = $line;
            else if(strstr(strtolower($line["id_pc"]),strtolower($search))) $table[] = $line;
        }
    }

    // $table = [];
    // for($i=0;$i<50;$i++){
    //     for($j=0;$j<6;$j++){
    //         $table[$i][] = "content".$i."-".$j;
    //     }
    // }

    $numberDisplayed = 5;

    $division = (count($table)%$numberDisplayed)
        ? (int)(count($table)/$numberDisplayed) + 1
        : (int)count($table)/$numberDisplayed; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="swiper-bundle.css" />
    <link rel="stylesheet" href="style.css"/>
    <title>Notifications</title>
</head>
<body>

    <nav>
        <div class="left">
            <div class="menu"></div>
            <a href="#">PC non remis</a>
        </div>
        <div class="right">
            <div class="box-search">
                <input type="text" name="search" id="global-search" placeholder="Recherche ..." value="<?php echo $search ?>">
                <button>S</button>
            </div>
            <div class="profil"></div>
        </div>
        <hr>
    </nav>

    <div class="box-blur">
        <div class="blur">
            <div class="red-blur" style="--pos:0%"></div>
            <div class="red-blur" style="--pos:12%"></div>
            <div class="red-blur" style="--pos:24%"></div>
        </div>
    </div>

<!-- 
    <form action="./presence_pc_table.php" method="get">
        <label for="date"></label><input type="date" name="date" value="<?php echo $date ?>">
        <button type="submit"></button>
    </form> -->

    <div class="stats">
        <!-- <div class="present">
            <h4>Nombre d'élèves présents</h4>
            <h2>123</h2>
        </div> -->
        <div class="absent">
            <h4>Nombre de PC non remis</h4>
            <h2><?php echo count($table); ?></h2>
        </div>
    </div>

    <div class="level-filter">
        <a href="./pc_non_remis.php?level=tous" <?php if($level == 'tous') echo "class=\"current-level\""?> >Tous</a>
        <a href="./pc_non_remis.php?level=l1" <?php if($level == 'L1') echo "class=\"current-level\""?>>L1</a>
        <a href="./pc_non_remis.php?level=l2" <?php if($level == 'L2') echo "class=\"current-level\""?>>L2</a>
        <a href="./pc_non_remis.php?level=l3" <?php if($level == 'L3') echo "class=\"current-level\""?>>L3</a>
        <a href="./pc_non_remis.php?level=m1" <?php if($level == 'M1') echo "class=\"current-level\""?>>M1</a>
        <a href="./pc_non_remis.php?level=m2" <?php if($level == 'M2') echo "class=\"current-level\""?>>M2</a>
    </div>

    <div class="box-folder">
        <div class="folder">
            <div class="legende-presence">
                    <div class="presence">
                        <div class="check absent checked" style="--color:#FF0404;"></div><label for="absent">Absent(e)</label>
                        <div class="check present checked" style="--color:#E7BB2D;"></div><label for="present">Present(e)</label>
                    </div>
            </div>
            <div class="top"> </div>
            <div class="body-block">
                <div class="box">
                    <table>
                        <tr>
                            <th>Prénoms</th>
                            <th>ID PC</th>
                            <th>Niveau</th>
                            <th>Retrait</th>
                            <th>Présence</th>
                        </tr>
                    </table>
                    <section class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                                for($i=0;$i<$division;$i++){
                                    ?>
                                        <div class="swiper-slide">
                                            <table>
                                                <?php
                                                    for($line=$i*$numberDisplayed;$line<($i+1)*$numberDisplayed && $line<count($table);$line++){
                                                        ?>
                                                            <tr>
                                                                <?php
                                                                    
                                                                    foreach($table[$line] as $key => $value){
                                                                        ?>
                                                                            <td> <?php 
                                                                                if($key === "presence"){
                                                                                    ?>
                                                                                        <div class="presence">
                                                                                            <div class="check absent <?php if(empty($value)) echo " checked" ?>" style="--color:#FF0404;"></div>
                                                                                            <div class="check present <?php if(!empty($value)) echo " checked" ?>" style="--color:#E7BB2D;"></div>
                                                                                        </div>
                                                                                    <?php
                                                                                }
                                                                                else echo $value; 
                                                                            ?> </td>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </tr>
                                                        <?php
                                                    }
                                                ?>
                                            </table>
                                        </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </section>
                </div>
                <div class="buttons-box">
                    <div class="swiper-prev"></div>
                    <div class="swiper-next"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="file" hidden>pc_non_remis.php</div>
    <script src="./swiper-bundle.js"></script>
    <script src="./app.js"></script>
</body>
</html>