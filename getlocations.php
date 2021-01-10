<?php
    session_start();
    require 'dbh.php';

    try {
         $sql = "";
         if(isset($_GET["val"])){
            $t = $_GET["val"];
            if($t==4){

               //$date2 = date("Y-m-d h:i:s", $time);
               // echo '</br>'.$date.'</br>'.$date2;
               $date1 = $_GET['date1'].' '.$_GET['time1'];
               $date1 = date("Y-m-d h:i:s", strtotime($date1));
               $date2 =  $_GET['date2'].' '.$_GET['time2'];
               $date2 = date("Y-m-d h:i:s", strtotime($date2));
              // echo $date1 .'<br/>'.$date2;
               //$sql = "SELECT * FROM missing_pets_posts where MP_ID='71'";
               //echo $date1.' '.$date2;
               $sql = "SELECT * FROM `missing_pets_posts` WHERE `MP_ID`='71' AND `post_time` BETWEEN '$date1' AND '$date2' ";
            }
            else{
                $sql = "SELECT * FROM `missing_pets_posts` WHERE `MP_ID`='71'";
            }
         }
         else{
            $sql = "SELECT * FROM `missing_pets_posts` WHERE `MP_ID`='71'";
         }
         
        $id = 71;
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $sth = $db->query($sql);
        $locations = $sth->fetchAll();

        echo json_encode( $locations );

    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
