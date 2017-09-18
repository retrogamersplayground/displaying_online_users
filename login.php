<?php
//login.php
include('database_connection.php');
if(isset($_SESSION["type"]))
{
 header("location: index.php");
}
$message = '';

if(isset($_POST["login"]))
{
 if(empty($_POST["email"]) || empty($_POST["password"]))
 {
  $message = "<label>Both Fields are required</label>";
 }
 else
 {
  $query = "
  SELECT * FROM user_details 
  WHERE email = :email
  ";
  $statement = $connect->prepare($query);
  $statement->execute(
   array(
    'email' => $_POST["email"]
   )
  );
  $count = $statement->rowCount();
  if($count > 0)
  {
   $result = $statement->fetchAll();
   foreach($result as $row)
   {
    if(password_verify($_POST["password"], $row["password"]))
    {
     $insert_query = "
     INSERT INTO login_details (
      memberID, last_activity) VALUES (
      :memberID, :last_activity)
     ";
     $statement = $connect->prepare($insert_query);
     $statement->execute(
      array(
       'memberID'  => $row["memberID"],
       'last_activity' => date("Y-m-d H:i:s", STRTOTIME(date('h:i:sa')))
      )
     );
     $login_id = $connect->lastInsertId();
     if(!empty($login_id))
     {
      $_SESSION["type"] = $row["user_type"];
      $_SESSION["login_id"] = $login_id;
      header("location: index.php");
     }
    }
    else
    {
     $message = "<label>Wrong Password</label>";
    }
   }
  }
  else
  {
   $message = "<label>Wrong Email Address</labe>";
  }
 }
}


?>

<!DOCTYPE html>
<html>
 <head>
  <title>How Display Users Online using PHP with Ajax JQuery</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br />
  <div class="container">
   <h2 align="center">How Display Users Online using PHP with Ajax JQuery</h2>
   <br />
   <div class="panel panel-default">
    <div class="panel-heading">Login</div>
    <div class="panel-body">
     <form method="post">
      <span class="text-danger"><?php echo $message; ?></span>
      <div class="form-group">
       <label>User Email</label>
       <input type="text" name="email" class="form-control" />
      </div>
      <div class="form-group">
       <label>Password</label>
       <input type="password" name="password" class="form-control" />
      </div>
      <div class="form-group">
       <input type="submit" name="login" value="Login" class="btn btn-info" />
      </div>
     </form>
    </div>
   </div>
  </div>
 </body>
</html>
