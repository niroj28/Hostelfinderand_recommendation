<?php 
session_start();
if(!isset($_SESSION["email"])){
  header("location:../index.php");
}

include("navbar.php");

 ?>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

 <div class="container-fluid">
  <ul class="nav nav-pills nav-justified">
    <li style="background-color: #FFF8DC"><a  href="admin-index.php">Home</a></li>
     <?php 
if(isset($_SESSION["email"]) && !empty($_SESSION['email'])){
  echo '<li><a href="../logout.php">Logout</a></li>';
}

else {?>
      <li><a href="../how-to-register.php"><span class="glyphicon glyphicon-user"></span> Register</a></li>
      <li><a href="../how-to-login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
    <?php } ?>
  </ul>
  <div class="tab-content">
    
    <div>
      <center style="margin-top:120px;"><h3>Review Detail</h3></center>
      <div class="container-fluid">
      

              <table id="myTable2">
                <tr class="header">
                  <!-- <th>Id.</th> -->
                  <th>Comment</th>
                  <th>Rating</th>
                  <th>Property Id</th>
                  <th>Positive %</th>
                  <th>Neutral %</th>
                  <th>Negative %</th>
                  <th>Compound %</th>
                </tr>
                <?php 
        include("../config/config.php");

        $sql="SELECT * from review";
        $result=mysqli_query($db,$sql);

        if(mysqli_num_rows($result)>0)
      {
          while($rows=mysqli_fetch_assoc($result)){
          
       ?>
                <tr>
                  <!-- <td><?php echo $rows['review_id'] ?></td> -->
                  <td><?php echo $rows['comment'] ?></td>
                  <td><?php echo $rows['rating'] ?></td>
                  <td><?php echo $rows['property_id'] ?></td>
                  <td><?php echo $rows['positive'] ?></td>
                  <td><?php echo $rows['neutral'] ?></td>
                  <td><?php echo $rows['negative'] ?></td>
                  <td><?php echo $rows['compound'] ?></td>
                </tr>
              <?php }} ?>
              </table>   
    </div>
    </div>


  </div>
</body>


