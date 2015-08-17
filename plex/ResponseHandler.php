<?php
	
	class ResponseHandler{



		public function getProductDataFromRawResponse( $rawResponse ){

			$productData=array();
			if(empty($rawResponse->ExecuteDataSourceResult->ResultSets)){
				return $productData; 
			}

			$dataArray = $rawResponse->ExecuteDataSourceResult->ResultSets->ResultSet->Rows->Row->Columns->Column;
			// echo"<br>--------------------- converting raw response to product data ----------------------<br>";
			// echo "<pre>";
			//print_r($rawResponse);
			//print_r($dataArray);
			// echo "</pre>";

			foreach ($dataArray as $key => $value) {
				//var_dump($value);
				$productData[$value->Name] = $value->Value;
			}

			// echo "<pre>";
			// print_r($productData);
			// echo "</pre>";
			
			return $productData;
		}

		public function getProductInventoryDataFromRawResponse( $rawResponse ){

			$productInvData;
			if(empty($rawResponse->ExecuteDataSourceResult->ResultSets)){
				return array(); 
			}
			$dataArray = $rawResponse->ExecuteDataSourceResult->ResultSets->ResultSet->Rows->Row->Columns->Column;
			// echo"<br>--------------------- converting raw response to product inventory data ----------------------<br>";
			// echo "<pre>";
			// //print_r($rawResponse);
			// print_r($dataArray);
			// echo "</pre>";

			$productInvData = $dataArray->Value;

			// echo "<pre>";
			// print_r($productInvData);
			// echo "</pre>";
			
			return $productInvData;
		}

		public function getNewPlexustomerNo( $rawResponse ){
			$customerNo="";



			return $customerNo;
		}

		public function getCustomerAddressResponse($rawResponse){

			$return_val = array();
			if(empty($rawResponse->ExecuteDataSourceResult->ResultSets)){
				return $return_val; 
			}

			$dataArray = $rawResponse->ExecuteDataSourceResult->ResultSets->ResultSet->Rows->Row;
			// echo "<pre>";
			// print_r($dataArray);
			// echo "</pre>";
			if(is_array($dataArray)){
				foreach ($dataArray as $key => $row) {
					array_push($return_val, $row->Columns->Column);
				}
			}
			else{
				array_push($return_val, $dataArray->Columns->Column);
			}

			return $return_val; 
		}

		public function getOrderNoFromRawResponse($rawResponse){
			$return_val = '';
			// echo "<pre>";
			// print_r($rawResponse);
			// echo "</pre>";
			if(empty($rawResponse->ExecuteDataSourceResult->OutputParameters)){
				return $return_val; 
			}

			$dataArray = $rawResponse->ExecuteDataSourceResult->OutputParameters->OutputParameter;
		
			foreach ($dataArray as $key => $row) {
				if($row->Name == '@Order_No')
				{
					$return_val = $row->Value;
					break;
				}
			}

			return $return_val; 
		}

		public function getOrderStatusFromRawResponse($rawResponse){
			$return_val = '';
			// echo "<pre>";
			// print_r($rawResponse);
			// echo "</pre>";

			if(empty($rawResponse->ExecuteDataSourceResult->ResultSets)){
				return $return_val; 
			}

			$dataArr =  $rawResponse->ExecuteDataSourceResult->ResultSets->ResultSet->Rows->Row->Columns->Column;

			foreach ($dataArr as $key => $value) {
				if( 'PO_Status' == $value->Name){
					return $value->Value;
				}
			}
			return $return_val; 
		}


	}



?>