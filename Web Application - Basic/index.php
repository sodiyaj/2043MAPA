<?php 
    require("config.php"); 
    $submitted_username = ''; 
    if(!empty($_POST)){ 
        $query = "CALL selectUser(?)"; 
        
        try{ 
            $stmt = $db->prepare($query); 
            $stmt->bindParam(1, $_POST['username'] , PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 4000); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $login_ok = false; 
        $row = $stmt->fetch(); 
        if($row){ 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            } 
            if($check_password === $row['password']){
                $login_ok = true;
            } 
        } 

        if($login_ok){ 
            unset($row['salt']); 
            unset($row['password']); 
            $_SESSION['user'] = $row;  
            header("Location: secret.php"); 
            die("Redirecting to: secret.php"); 
        } 
        else{ 
            print("Login Failed."); 
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8'); 
        } 
    } 
?> 

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>Haven</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">

        <form class="form-signin" action="index.php" method="post">
           <h2 class="form-signin-heading">Please sign in</h2>
           
            <input type="text" id="inputUsername" placeholder="Username" name="username" class="form-control" value="<?php echo $submitted_username;?>"/>
            
              <input type="password" id="inPassword" placeholder="Password" name="password" value="" class="form-control">
            
            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Login" /> 
          </form>    
        <p><a href="register.php">Click Here to register</a></p>
    </div> <!-- /container -->
  </body>
</html>