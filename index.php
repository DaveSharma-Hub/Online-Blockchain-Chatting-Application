<?php
    session_start();
    if(isset($_GET['logout'])){
         $_SESSION['name']="";
        session_destroy();
        header("Location:index.php");
    }

    if(isset($_GET['mine'])){
         $username="";
         $password="";
         $schema="";
         $conn = new mysqli("localhost",$username,$password,$schema);
        
        if($conn->connect_error){
            die($conn->connect_error);
        }else{

            $stmtMine = $conn->prepare("select * from chain");
            $stmtMine->execute();
            $stmtMine_result=$stmtMine->get_result();
            while($mined=$stmtMine_result->fetch_array()){
                if($mined['userID']!=1){
                $uName = $mined['userName'];
                $uData = $mined['userData'];
                $cHash = $mined['currentHash'];
                $pHash = $mined['previousHash'];
                $lTime = $mined['longTime'];
                $n = $mined['nonce'];

                $currentHashTest = hash('sha256',$pHash.$lTime.$n.$uData);
                    if($currentHashTest!=$cHash){
                        echo "<p>Blockchain Vulnerable</p>";
                        $_SESSION['name']="";
                        session_destroy();
                        header("Location:index.php");

                    }
                }
            }
            echo "<button onclick='perfectMine()'>Results</button>";
        }
         header("Location:index.php");
        }
    if(isset($_POST['enter'])){
        if($_POST['name']!=""){
            $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
         }
         else{
             echo '<span class="error">Please type in a name</span>';
         }
    }

    function loginForm(){
        

        echo '
            <div id="loginform">
            <p> Please enter you name</p>
                <form action="index.php" method="POST">
                    <label for="name">Name &mdash;</label>
                    <input type="text" name="name" id="name" />
                    <input type="submit" name="enter" id="enter" value="Enter" />
                </form>
             </div>
        ';
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
 
        <title>Blockchain Chat Application</title>
        <meta name="description" content="Blockchain Chat Application" />
        <link rel="stylesheet" href="styles.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@100&display=swap" rel="stylesheet"> 
    
    </head>
    <body>

    <?php
    if(!isset($_SESSION['name'])){
        loginForm();
    }
    else{
    ?>
        <div id="miner">
            <p class="mine"><a id="mine" href="#">Mine Chat</a></p>
        </div>
        <div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name'] ?><b></b></p>
                <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
            </div>
 
            <div id="chatbox">
                <?php
                    $username="";
                    $password="";
                    $schema="";
                    $conn = new mysqli("localhost",$username,$password,$schema);                            
                    if($conn->connect_error){
                        die($conn->connect_error);
                    }else{
                        $stmt=$conn->prepare("select * from chain");
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
            </div>

            
            <form name="message" action="post.php" method="POST">
                <input name="usermsg" type="text" id="usermsg" placeholder="Message"/>
                <input name="submitmsg" type="image" id="submitmsg" alt="" src="message.jpg" width="10%"/>
                  
            </form>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
            
        $(document).ready(function () {
            
            function getData(){
            $.ajax({
                type: 'POST',
                url: 'asynchronous.php',
                success: function(data){
                    $('#chatbox').html(data);
                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
                            //if(newscrollHeight > oldscrollHeight){
                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
                       // }   
                    }
                });
            }
            getData();
            setInterval(function () {
                getData(); 
            }, 1000);  // it will refresh your data every 1 sec

            $("#mine").click(function () {
                    var exit = confirm("Are you sure you want to mine the chat?");
                    if (exit == true) {
                    window.location = "index.php?mine=true";
                    }
            });

            $("#exit").click(function () {
                    var exit = confirm("Are you sure you want to end the session?");
                    if (exit == true) {
                    window.location = "index.php?logout=true";
                    }
            });
        });
        function failedMine() {
            alert("Problem in blockchain!");
            }
        function perfectMine() {
            alert("Blockchain Secure!");
            }
        </script>
    </body>
</html>
<?php
    }
?>
