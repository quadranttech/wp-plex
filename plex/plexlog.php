<?php

	class PlexLog{
		
		public static function addLog($logdata){

			//echo"------------------------------------------writting log --------------------------";
			$logContent ="". date("D M d, Y G:i::sa").' ==> '.$logdata ;
			file_put_contents("wp-content/plugins/plex-wp/plex/plexlogf.txt", $logContent . PHP_EOL, FILE_APPEND);

		}

	}

?>