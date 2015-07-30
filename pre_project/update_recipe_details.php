<?php 
//enters recipe name and number of products	
	//temporaliry!
$username = 'kokolina';
require_once('functions.php');
connect_database($connect);
if (!empty($_GET)) {
	$id_rec = $_GET['id_rec'];
	$num = $_GET['num'];
	//данните, вече въведени за описанието на рецептата и нейните продукти и за евентуална промяна
	$q_rec = "SELECT * FROM `recipes` WHERE `id` = $id_rec";
	$res_rec = mysqli_query($connect, $q_rec);
	$row_rec = mysqli_fetch_assoc($res_rec);
//	 и нейните продукти и за евентуална промяна
	$prod_info = array(array());
	$q_prod = "SELECT * FROM `recipe_products_quantities` WHERE `recipe_id` = $id_rec ORDER BY `id`";
	$res_prod = mysqli_query($connect, $q_prod);
	$i = 0;
	if (mysqli_num_rows($res_prod) > 0) {
		while($row_prod = mysqli_fetch_assoc($res_prod)) {
			foreach ($row_prod as $key => $value) {
				$prod_info[$i][$key] = $value;
			}
			$i++;			
		}
	}

} else {
	$id_rec = "";
	$num = "";
}
/*echo "<pre>";
var_dump($prod_info);
echo "</pre>";*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>	
	<form method="post" action="update_recipe_details.php">
		<input type="hidden" name="id_rec" value="<?php echo $id_rec; ?>">
		<input type="hidden" name="num_prod" value="<?php echo $num;?>">
		<input type="hidden" name="id" value=<?php if (isset($prod_info[0]['id'])) {echo $prod_info[0]['id'];}?>>
		<!--recipe products and measures-->
		<?php 
		for ($j=0; $j < $num; $j++) { 
			echo $j.' ';
			echo $prod_info[$j]['product_id'].' ';
			$q = "SELECT * FROM `products` ORDER BY `product` ";
			$result = mysqli_query($connect, $q);
			$pr = $j+1;
			echo "<p><label for='prod'".$j."'>Продукт".$pr."</label>";
			echo "<select name='product[]' id='prod".$j."'>";
			echo $j.' ';
			echo $prod_info[$j]['product_id'].' ';
			if (mysqli_num_rows($result)>0) {
				while ($row = mysqli_fetch_assoc($result)) {	
					$product = $row['product'];
					$prod_id = $row['id'];				
					echo "<option value='$prod_id'";
					echo $j;
					echo $prod_info[$j]['product_id'];
					if ($prod_id == $prod_info[$j]['product_id']) {
						echo " selected";
					} else {
						echo "";
					}
					echo ">".$product."</option>";
				}
			}
			echo "</select></p>";	
			$q_m = "SELECT * FROM `measures` ORDER BY `measure`";
			$result_m = mysqli_query($connect, $q_m);
			echo "<p><label for='meas'".$j."'>мярка</label>";
			echo "<p><select name='measure[]' id='meas".$j."'>";
			if (mysqli_num_rows($result_m)>0) {
				while ($row_m = mysqli_fetch_assoc($result_m)) {
					$measure = $row_m['measure'];
					$meas_id = $row_m['id'];						
					echo "<option value='$meas_id'";
					if ($prod_info[$j]['measures_id'] == $meas_id) {
						echo " selected";
					}
					echo ">".$measure."</option>";
				}
			}
			echo "</select></p>";
				//TODO labels
			$quantity = $prod_info[$j]['quantity'];
			echo "<input type='number' name='quantity[]' value = '$quantity'>";
		}


		?>
		<label for="description">Начин на приготвяне на рецептата</label>
		<textarea name="description" id="description" value="<?php echo $row_rec['description']; ?>">
			<?php  if (isset ($row_rec['description'])){ echo $row_rec['description']; }?>
		</textarea>
		<input type="submit" value="submit" name="submit">
	</form>
	<?php
	if (isset($_POST['submit'])) {
		//трябва ли ми???
		$id_rec = $_POST['id_rec'];
		$id = $_POST['id'];
		$quantity =  $_POST['quantity'];
		
		$measure = $_POST['measure'];
		
		$p = $_POST['product'];
		$description = $_POST['description'];
	//num of prod
		$num = $_POST['num_prod'];
		echo "<pre>";
		var_dump($quantity);
		var_dump($measure);
		var_dump($p);
			echo "</pre>";
		echo $num.' ';
		echo $id_rec;
		$flag = 0;
		$flag_des = 0;
		for ($k = 0; $k < $num; $k++) { 
				echo $p[$k]." ";
				echo $measure[$k]." ";
				echo $quantity[$k]."<br /> ";
			$q_r = "UPDATE `recipe_products_quantities` 
					SET `quantity`= $quantity[$k],
					`product_id` = $p[$k],
					`measures_id` = $measure[$k]
					WHERE `id`=$id";
			 
			if (mysqli_query($connect, $q_r)) {
				$flag = 1;
			} else {
				$flag = 0;
			}
			$id++;
		}
		//запис на описанието на рецептата
		$q_des = "UPDATE `recipes` 
		SET `description`='$description'
		WHERE `id` = $id_rec";
		if (mysqli_query($connect, $q_des)) {
			$flag_des = 1;
		} else {
			$flag_des = 0;
		}
		if ($flag == 1 && $flag_des == 1) {
			echo "Успешно записахте всички продукти и описание!";
		} else {
			echo "Опитайте отново!";
		}

	}
	?>


</body>
</html>