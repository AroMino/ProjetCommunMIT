<?php
    session_start();

    if(isset($_GET['date'])) $date = $_GET['date'];
    if(empty($date)) $date = date('Y-m-d',time()-24*3600);

    if(isset($_GET['level'])) $level = $_GET['level'];
    else $level = $_SESSION['level'];
    if(empty($level)) $level = "tous";
    $_SESSION['level'] = $level;

    if(isset($_GET['search'])) $search = $_GET['search'];
    else $search = $_SESSION['search'];
    $_SESSION['search'] = $search;

    /// Requete
    $connexion = mysqli_connect("localhost","mit","123456","mit")
    or die("Echec de connexion");

    /// nombre des absents

    $query = "select prenoms,machine_etudiants.id_machine as pc,inscription.grade,inscription.niveau,stat_pc.date_retrait as retrait
    ,stat_pc.date_remise as remise,presence.statut as presence from personnes
                inner join etudiants on
                    etudiants.id_personne=personnes.id_personne
                inner join inscription on
                    inscription.id_etudiant=etudiants.id_etudiant
                left join presence on 
                    inscription.id_inscription = presence.id_etudiant
                left join machine_etudiants on
                    machine_etudiants.id_inscription_etudiant=inscription.id_inscription
                left join stat_pc on machine_etudiants.id_machine=stat_pc.id_pc_etudiant
                where presence.date_presence like '2024-03-27'";

    $result = mysqli_query($connexion,$query);
    $count = 0;
    $count_absent = 0;
    $table = [];
    while($line = mysqli_fetch_assoc($result)){
        $line["date"] = $yesterday;
        $line["presence"] = "";
        $line['grade'] = $line['grade'].$line['niveau'];
        unset($line['niveau']);

        if($line['grade'] === $level || $level === 'tous'){
            if(strstr(strtolower($line["prenoms"]),strtolower($search))) $table[] = $line;
            else if(strstr(strtolower($line["id_pc"]),strtolower($search))) $table[] = $line;

            if(empty($line[$presence])) $count_absent++;
            $count++;
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
    <title>Presence</title>
</head>
<body>
    <nav>
        <div class="left">
            <div class="menu"></div>
            <a href="#"> Presence</a>
        </div>
        <div class="right">
            <div class="box-search">
                <input type="text" name="search" id="global-search" placeholder="Recherche ..." value="<?php echo $search ?>">
                <button></button>
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

    <form action="./presence.php" method="get">
        <label for="date"></label><input type="date" name="date" value="<?php echo $date ?>" id="date" class="date" onkeydown="return false">
        <input type="text" name="search" hidden value="">
        <button type="submit"></button>
    </form>

    <div class="stats">
        <div class="present">
            <h4>Nombre d'élèves présents</h4>
            <h2><?php echo $count - $count_absent; ?></h2>
        </div>
        <div class="absent">
            <h4>Nombre d'élèves absents</h4>
            <h2><?php echo $count_absent; ?></h2>
        </div>
    </div>

    <div class="level-filter">
        <a href="./presence.php?level=tous" <?php if($level == 'tous') echo "class=\"current-level\""?> >Tous</a>
        <a href="./presence.php?level=L1" <?php if($level == 'L1') echo "class=\"current-level\""?>>L1</a>
        <a href="./presence.php?level=L2" <?php if($level == 'L2') echo "class=\"current-level\""?>>L2</a>
        <a href="./presence.php?level=L3" <?php if($level == 'L3') echo "class=\"current-level\""?>>L3</a>
        <a href="./presence.php?level=M1" <?php if($level == 'M1') echo "class=\"current-level\""?>>M1</a>
        <a href="./presence.php?level=M2" <?php if($level == 'M2') echo "class=\"current-level\""?>>M2</a>
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
                            <th>Remise</th>
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
    <div class="file" hidden>presence.php</div>
    <script src="./swiper-bundle.js"></script>
    <script src="./app.js"></script>
</body>
</html>