<?php

    session_start();
//$msg= $_POST['usermsg'];

    if(isset($_SESSION['name'])){
        $name = $_SESSION['name'];
        $text = $_POST['usermsg'];
        $message = $text;
    //     $text_message = "<div class='msgln'><span class='chat-time'>".date("g:i A")."</span> <b class='user-name'>".$_SESSION['name']."</b> ".stripslashes(htmlspecialchars($text))."<br></div>";
    //    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
       
        $conn = new mysqli("localhost","dave(2)","ensf409","blockchain");
        
        if($conn->connect_error){
            die($conn->connect_error);
        }else{
            $statement = $conn->prepare("select * from chain");
            $statement->execute();
            $statement_result = $statement->get_result();

            $largestID=-1;
            while($data= $statement_result->fetch_array()){
                if($data['userID']>$largestID){
                    $largestID=$data['userID'];
                }
            }
            if($largestID!=-1){
                $statement = $conn->prepare("select * from chain where userID=?");
                $statement->bind_param("i",$largestID);
                $statement->execute();
                $statement_result = $statement->get_result();
                if($statement_result->num_rows>0){
                    $previous = $statement_result->fetch_assoc();
                    
                    //Define cipher 

                    $previousHash = $previous['currentHash'];
                    $nonce = intval($previous['nonce'])+1;
                    $longTime = time();

                    $currentHash = hash('sha256',$previousHash.$longTime.$nonce.$text);

                    $stmt = $conn->prepare("insert into chain (userName,userData,currentHash,previousHash,longTime,nonce) values(?,?,?,?,?,?)");
                    $stmt->bind_param("ssssii",$name,$message,$currentHash,$previousHash,$longTime,$nonce);
                    $stmt->execute();
                    header("Location:index.php");
                }
            }
        }
    }
?>