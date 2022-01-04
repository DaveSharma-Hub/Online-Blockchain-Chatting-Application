
<?php
        session_start();

        $username="";
        $password="";
        $schema="";
        $connection = new mysqli("localhost",$username,$password,$schema);
                
        if($connection->connect_error){
            die($connection->connect_error);
        }else{
            $stmt=$connection->prepare("select * from chain");
            $stmt->execute();
            $stmt_result=$stmt->get_result();
            while($data=$stmt_result->fetch_array()){
                if($data['userID']!=1){
                    if($data['userName']!=$_SESSION['name']){
                        echo "<div class='msgln'><span class='chat-time'>".date('m/d/Y', $data['longTime'])."</span> <b class='user-name'>".$data['userName']."</b><b class='user-data'>".$data['userData']."</b><br></div>";
                    }
                    else{
                        echo "<div class='msglnright'><span class='chat-time'>".date('m/d/Y', $data['longTime'])."</span> <b class='user-name'>".$data['userName']."</b><b class='user-data'>".$data['userData']."</b><br></div>";
                    }                            
                }
            }
        }
?>
