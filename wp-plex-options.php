<?php
/**
 * Add an option page
 */
require_once (dirname(__FILE__).'/model/plexdatamodel.php');

function plex_config_options() 
{

	$plexDataModelObj = new PlexDataModel();
	$err=false;
	$err_msg='';

	$success=false;
	$success_msg='';


	if( isset($_POST['plexcredentials']) && 1 == $_POST['plexcredentials'] ){

		if( $_POST['plexusername']=='' || $_POST['plexpassword']== '' || $_POST['plexwsdllink']=='' ){
			$err=true;
			$err_msg='Please fill all the fields';
		}
		else{
			
			$res = $plexDataModelObj->setPlexCredentials( $_POST['plexusername'],  $_POST['plexpassword'],  $_POST['plexwsdllink'] );
			if($res){
				$success=true;
				$success_msg='Saved Successfully';
			}
			else{
				$err=true;
				$err_msg='Unable to save data. Please try again.';
			}
		}

	}
	
	$res = $plexDataModelObj->getPlexCredentials();

	?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32">
			<br>
		</div>
		<h2 style="background: url(../wp-content/plugins/wp-plex/px-logo.png) no-repeat 0 0;padding: 6px 0 0 50px;">
			<?php _e("Plex Configuration Options", PLEX_CONFIG_DOMAIN); ?>
		</h2>

		<div class="configFormHolder">
			<h3>Please fill the form with required credentials.</h3>
			<?php 
				if($err){
					?>
					<div class="err_msg"><?php echo $err_msg;?></div>
					<?php
				}
				else if($success){
					?>
					<div class="success_msg"><?php echo $success_msg;?></div>
					<?php
				} 
			?>
			<form action="" method="post">
				<input type="text" name="plexusername" placeholder="Plex Username" value="<?php if(isset($res['username'])){echo $res['username'];} ?>" >
				<input type="text" name="plexpassword" placeholder="Plex Password" value="<?php if(isset($res['password'])){echo $res['password'];} ?>" >
				<input type="text" name="plexwsdllink" placeholder="Plex wsdl link" value="<?php if(isset($res['wsdl_link'])){echo $res['wsdl_link'];} ?>" >
				<input type="hidden" name="plexcredentials" value="1">
				<input type="submit" value="submit">
			</form>
		</div>
	</div>


	<?php
}
?>