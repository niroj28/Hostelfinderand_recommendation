<?php 


$db = new mysqli('localhost','root','','hostelfinder');

if($db->connect_error){
  echo "Error connecting database";
}

if(isset($_POST['delete_property'])){
		
$property_id=$_POST['property_id'];


$sql="DELETE from property_photo where property_id='$property_id'";
$query=mysqli_query($db,$sql);

if($query){
	$sql2="DELETE from review where property_id='$property_id'";
$query2=mysqli_query($db,$sql2);

$sql3="DELETE from add_property where property_id='$property_id'";
$query3=mysqli_query($db,$sql3);
if($query3){
			
?>

<style>
.alert {
  padding: 20px;
  background-color: #DC143C;
  color: white;
}

.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
<script>
	window.setTimeout(function() {
    $(".alert").fadeTo(1000, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>
<div class="container">
<div class="alert" role='alert'>
  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
  <center><strong>Your Product has been deleted.</strong></center>

</div></div>


<?php

}
}} ?>

<div style="padding: 10px;margin: 10px;display: flex;justify-content: center; text-decoration: none;">
<button><a href="owner-index.php" style="padding: 10px;margin: 10px;display: flex;justify-content: center; text-decoration: none;">Return to homepage</a></button>
</div>

