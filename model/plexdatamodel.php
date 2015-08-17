<?php
	global $wpdb;

	/**
	* To work with plex data table
	*/
	class PlexDataModel
	{
  		private $db = NULL;
		
		function __construct()
		{
    		$this->dbConnect();// Initiate Database connection			
			$this->createPlexCredentialTable();
		}

		private function dbConnect(){
			$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
			$username = DB_USER;
			$password = DB_PASSWORD;
			$this->db = new PDO($dsn, $username, $password);		
	  	}

	  	private function createPlexCredentialTable()
	    {
			if(empty($this->db)){
				return false;
			}
			else{
				$ret = $this->db->exec('
				          				CREATE TABLE IF NOT EXISTS plex_credentials( 	id    INT AUTO_INCREMENT,
							                                            				username  VARCHAR(255),
							                                            				password  VARCHAR(255),
							                                            				wsdl_link TEXT,
							                                            
							                                            				PRIMARY KEY (id)
								                                           			)
										');	

				if($ret){
					return true;
				}
				else{
					return false;
				}
			}
	  	}


		public function getPlexCredentials(){
  			$quert_str = 'SELECT * FROM `plex_credentials` WHERE id=1';
  			$res = $this->db->query($quert_str);
  			$data  = $res->fetch();
  			return $data;

		}

		public function setPlexCredentials($username,$password,$wsdl_link){


			$quert_str = 'INSERT INTO `plex_credentials` (id,username, password, wsdl_link) VALUES(1,"'.$username.'","'.$password.'","'.$wsdl_link.'") ON DUPLICATE KEY UPDATE username="'.$username.'", password="'.$password.'", wsdl_link="'.$wsdl_link.'"';
			
			$ret = $this->db->exec( $quert_str );
			return $ret;
		}


	}



?>