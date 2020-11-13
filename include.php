<?php

/*
███▄▄▄▄      ▄████████    ▄████████    ▄████████  ▄█    ▄▄▄▄███▄▄▄▄   
███▀▀▀██▄   ███    ███   ███    ███   ███    ███ ███  ▄██▀▀▀███▀▀▀██▄ 
███   ███   ███    ███   ███    █▀    ███    █▀  ███▌ ███   ███   ███ 
███   ███   ███    ███   ███          ███        ███▌ ███   ███   ███ 
███   ███ ▀███████████ ▀███████████ ▀███████████ ███▌ ███   ███   ███ 
███   ███   ███    ███          ███          ███ ███  ███   ███   ███ 
███   ███   ███    ███    ▄█    ███    ▄█    ███ ███  ███   ███   ███ 
 ▀█   █▀    ███    █▀   ▄████████▀   ▄████████▀  █▀    ▀█   ███   █▀  
                                                                     
*/																	 

error_reporting(E_ALL);
session_start();
/*On suppose que l'on s'est connecté avec l'utilisateur nas92.*/
$_SESSION["nom"] = "nas92";
/*Connexion à la base de donnée*/
try {
$co = new PDO('mysql:host=localhost;dbname=chat', "root", "");
$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo $e->getMessage();
}
/*Pour éviter les problèmes faites de même dans la base de donnée.*/
$u1 = NULL;
$u2 = NULL;
$u3 = NULL;

/*Vérification des champs <input type='hidden' />.*/
if(isset($_POST["id"])){
	if(isset($_POST["user1"]))
	{
		if(!empty($_POST["user1"]))
		{
			   if($_POST["user1"] != $u2 && $_POST["user1"] != $u3)
				$u1 = htmlspecialchars(trim(stripslashes(strip_tags($_POST["user1"]))));
		
		if(isset($_POST["user2"]))
		{
			if(!empty($_POST["user2"]))
			{
				if($_POST["user2"] != $u1 && $_POST["user2"] != $u3)
					$u2 =  htmlspecialchars(trim(stripslashes(strip_tags($_POST["user2"]))));
			}
		}
		
		if(isset($_POST["user3"]))
		{
			if(!empty($_POST["user3"]))
			{
				if($_POST["user3"] != $u1 && $_POST["user3"] != $u2)
				$u3 =  htmlspecialchars(trim(stripslashes(strip_tags($_POST["user3"]))));
			}
		}
	
/*Insértion si tout s'est bien passé on utilise la fonction mt_rand pour donner un nombre entre 0 et 5000000 on s'en servira pour afficher les groupes.*/	
		$insertgroupe = $co->prepare("INSERT INTO groupe(groupe_id,user1,user2,user3) VALUES(?,?,?,?)");
		$insertgroupe->execute(array(mt_rand(0,5000000),$_POST["user1"],$u2,$u3));
		echo "<font color='green'>Effectué avec succès</font>";
		}
	}else
		echo "<font color='red'>Wesh t'abuse  frère rentre 1 truc au moins</font>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <!--Un peu de bootstrap ça fait pas de mal.-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <title>Chat Groupe</title>
</head>
<body>

<!-- On range dans un select la valeur des membres je vous laisse décider de ce que vous voulez ici.-->
<form action="" method="POST" id="form">
	<select name="id" id="id" onClick='placeValue()'>
		<option value="0">Choisit un membre</option>
		<?php 
		$querymember = $co->query("SELECT * FROM membre");
		while($qm = $querymember->fetch())
		{
				echo "<option value=".$qm["identifiant"].">".$qm["identifiant"]."</option>";
		}
		?>
	</select>
	
	<input type="submit" id="add" class="btn btn-success" value="Créer groupe"/>
</form>

<!--On affiche dans l'ordre décroissant les groupes de sorte à ce que le dernier soit toujours en haut.-->
<?php 

$querygroup = $co->prepare("SELECT * FROM groupe WHERE user1 = ? OR user2 = ? OR user3 = ? ORDER BY id DESC");
$querygroup->execute(array($_SESSION["nom"],$_SESSION["nom"],$_SESSION["nom"]));

/*i c'est notre compteur.*/
$i = $querygroup->rowCount();
while($qg = $querygroup->fetch())
	echo "<a href='".$qg["groupe_id"]."'>Votre groupe n°".$i--."</a><br><br>";
?>
	
		<script>
		   /*On cherche l'id du select.*/
			var id = document.getElementById("id");
			/*On prend l'id du formulaire pour lui ajouter des input de type hidden.*/
			var form = document.getElementById("form");
			/*i servira pour l'incrémentation des utilisateurs */
			var i = 1;
			
			/*Déclanchement de la fonction à chaque click du select*/
			function placeValue(){
				/*Si la valeur du select n'est pas à 0 et que que l'utilisateur3 n'existe pas.*/
				if(!(document.getElementById("user3")) && id.value != 0)
				{
					/*On injecte dans le formulaire de input de type hidden avec l'id 'user'+1 et name='nom de la valeur du select ici un nom de membre'.*/
						var champ = document.createElement('input');
						champ.setAttribute('type','hidden');
						champ.setAttribute('value',id.value);
						champ.setAttribute('name','user'+i++);
						form.appendChild(champ);
						/*Question de sécurité on retire de sorte à ce que l'utilisateur ne met pas la même value on va supprimer la valeur où il a cliqué dans le select.*/
						id.remove(id.selectedIndex);
						/*On remet la valeur du select à 0 comme ça l'utilisateur voit Choisit un champ et sélectionne.*/
						id.value = 0;
				}
				
			}
		</script>
</body>
</html>