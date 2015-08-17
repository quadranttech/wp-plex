<?php
require_once('SoapClientWrapper.php');

class plexServiceRequest{

	private $username = "plex username";
	private $password = "plex password";
	private $soap_version  = SOAP_1_2;
	private $wsdl = 'https://testapi.plexonline.com/DataSource/Service.asmx?WSDL'; /*test*/
	private $client;

	public function __construct() {
    	/* create a soap client object*/   
    	$credentials = array(
    							'trace'         => 1, 
								'exception'    	=> 1,
								'cache_wsdl'   	=> 0,
								'login'			=> $this->username,
								'password'		=> $this->password,
								'soap_version' 	=> $soap_version,
							);
		
		$this->client  = new SoapClientWrapper($this->wsdl, $credentials);
   	}

   	public function call($method ,$dataSourceKey, $paramArray){
  		
   		$parm_xml='';
   		$parm_xml  .= '<ExecuteDataSource xmlns="http://www.plexus-online.com/DataSource">';
		$parm_xml  .= '<ExecuteDataSourceRequest>';
		$parm_xml  .= '<DataSourceKey>'.$dataSourceKey.'</DataSourceKey>';
		$parm_xml  .= '<InputParameters>';

		foreach ($paramArray as $param) {
			$parm_xml  .= '	<InputParameter>';
			
			foreach ($param as $key => $value) {
				$parm_xml  .= '<'.$key.'>'.$value.'</'.$key.'>';
			}
			$parm_xml  .= '	</InputParameter>';

		}	
		$parm_xml  .= '</InputParameters>';		
		$parm_xml  .= '</ExecuteDataSourceRequest>';
		$parm_xml  .= '</ExecuteDataSource>';

		$input_parameters  = new SoapVar($parm_xml, XSD_ANYXML);

		try {
			$response = $this->client->__soapCall( $method, array($input_parameters) );  
			return $response;
		} catch (Exception $e) {
            return 0;
        }
   	}
}
?>