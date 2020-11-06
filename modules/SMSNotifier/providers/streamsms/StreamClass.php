<?php		
	class STREAM
	{		
 		/*------------------------------------------------------------------------------------------------*/
		/*		Функция получения идентификатора сессии													  */
		/*	$login (string) - логин пользователя														  */
		/*	$password (string) - пароль пользователя													  */
		/*------------------------------------------------------------------------------------------------*/
		
		function GetSessionId($server,$login,$password)
		{
			$href = $server.'Session/?login='.$login.'&password='.$password;			
			$result = $this -> GetConnect($href);
			return json_decode($result,true);
		} 

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция получения баланса пользователя													  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*------------------------------------------------------------------------------------------------*/
		function GetBalance($server,$session)
		{
			$href = $server.'Balance/?sessionId='.$session;			
			$result = $this -> GetConnect($href);
			return json_decode($result,true);
		}
		
		/*------------------------------------------------------------------------------------------------*/
		/*		Функция получения статистики по отправленным сообщениям									  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$startDateTime (DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - начало периода для запроса статистики		  */
		/*	$endDateTime (DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - конец периода для запроса статистики		  */
		/*------------------------------------------------------------------------------------------------*/
		function GetStatistic($server,$session,$startDateTime,$endDateTime)
		{
			$href = $server.'Statistic/?sessionId='.$session.'&startDateTime='.$startDateTime.'&endDateTime='.$endDateTime;			
			$result = $this -> GetConnect($href);
			return json_decode($result,true);
		}
		
		/*------------------------------------------------------------------------------------------------*/
		/*		Функция получения входящих сообщений													  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$minDateUTC (DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - начало периода для запроса входящих сообщений  */	
		/*	$maxDateUTC (DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - конец периода для запроса входящих сообщений	  */
		/*------------------------------------------------------------------------------------------------*/
		function GetIncomingSms($server,$session,$minDateUTC,$maxDateUTC)
		{
			$href = $server.'Incoming/?sessionId='.$session.'&minDateUTC='.$minDateUTC.'&maxDateUTC='.$maxDateUTC;			
			$result = $this -> GetConnect($href);
			$result = $this -> ChangeFormateDate(json_decode($result,true));
			return $result;			
		}
		
		/*------------------------------------------------------------------------------------------------*/
		/*		Функция получения статуса сообщения														  */
		/*	$session (string 32 символа) - идентификатор пользователя 	  								  */
		/*	$messageId (string 9 символов) - статус отправленного сообщения 							  */
		/*------------------------------------------------------------------------------------------------*/
		function GetState($server,$session,$messageId)
		{
			$href = $server.'State/?sessionId='.$session.'&messageId='.$messageId;			
			$result = $this -> GetConnect($href);
			$result = $this -> ChangeFormateDate(json_decode($result,true));
			return $result;
		}
		
		/*------------------------------------------------------------------------------------------------*/
		/*		Функция отправки единичного сообщения													  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$sourceAddress (string 11 латинских или не более 15 цифровых символов) - имя отправителя	  */
		/*	$destinationAddress (integer) - номер абонента (в формате 79111234567 для РФ)				  */
		/*	$data (string) - текст сообщения															  */
		/*	$validity 	(integer) - время жизни сообщения, указывается в минутах, по умолчанию 1440 	  */
		/*				(необязательный параметр)														  */
		/*	$sendDate 	(DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - время передачи сообщения						  */
		/*				(необязательный параметр)														  */
		/*------------------------------------------------------------------------------------------------*/
		function SendSms($server,$session,$sourceAddress,$destinationAddress,$data,$validity,$sendDate = '')
		{
			$href = $server.'Send/SendSms/';
			if($sendDate != '')
			$sendDate = '&sendDate='.$sendDate;
			$src = 'sessionId='.$session.'&sourceAddress='.$sourceAddress.'&destinationAddress='.$destinationAddress.'&data='.$data.'&validity='.$validity.$sendDate;
			$result = $this -> PostConnect($src,$href);						
			return json_decode($result,true);
		}

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция отправки сообщений нескольким адресатам											  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$sourceAddress (string 11 латинских или не более 15 цифровых символов) - имя отправителя	  */
		/*	$destinationAddresses (string) - номера абонентов (номера указываются через запятую)			  */
		/*	$data (string) - текст сообщения															  */
		/*	$validity 	(integer) - время жизни сообщения, указывается в минутах, по умолчанию 1440 	  */
		/*				(необязательный параметр)														  */
		/*------------------------------------------------------------------------------------------------*/
		function SendBulk($server,$session,$sourceAddress,$destinationAddresses,$data,$validity)
		{
			$href = $server.'Send/SendBulk/';			
			$src = 'sessionId='.$session.'&sourceAddress='.$sourceAddress.'&destinationAddresses='.$destinationAddresses.'&data='.$data.'&validity='.$validity;
			$result = $this -> PostConnect($src,$href);						
			return json_decode($result,true);
		}

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция отправки сообщений по локальному времени абонента								  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$sourceAddress (string 11 латинских или не более 15 цифровых символов) - имя отправителя	  */
		/*	$destinationAddress (integer) - номер абонента (в формате 79111234567 для РФ)				  */
		/*	$data (string) - текст сообщения															  */
		/*	$validity 	(integer) - время жизни сообщения, указывается в минутах, по умолчанию 1440 	  */
		/*				(необязательный параметр)														  */
		/*	$sendDate 	(DateTime ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС) - время передачи сообщения по UTC				  */
		/*------------------------------------------------------------------------------------------------*/
		function SendByTime($server,$session,$sourceAddress,$destinationAddress,$data,$validity,$sendDate = '')
		{
			$href = $server.'Send/SendByTime/';
			if($sendDate != '')
			$sendDate = '&sendDate='.$sendDate;
			$src = 'sessionId='.$session.'&sourceAddress='.$sourceAddress.'&destinationAddress='.$destinationAddress.'&data='.$data.'&validity='.$validity.$sendDate;
			$result = $this -> PostConnect($src,$href);						
			return json_decode($result,true);
		}

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция пакетной отправки сообщений														  */
		/*	$session (string 32 символа) - идентификатор пользователя 									  */
		/*	$sourceAddress (string 11 латинских или не более 15 цифровых символов) - имя отправителя	  */
		/*	$destinationAddress (integer) - номер абонента (в формате 79111234567 для РФ)				  */
		/*	$phone_data (array) - тексты сообщений и номера получателей									  */
		/*	$validity 	(integer) - время жизни сообщения, указывается в минутах, по умолчанию 1440 	  */
		/*				(необязательный параметр)														  */
		/*------------------------------------------------------------------------------------------------*/
		function SendBulkPacket($server,$session,$sourceAddress,$phone_data,$validity)
		{
			$href = $server.'Send/SendBulkPacket/';			
			$src = 'sessionId='.$session.'&sourceAddress='.$sourceAddress.'&phone_data='.$phone_data.'&validity='.$validity;
			$result = $this -> PostConnect($src,$href);						
			return json_decode($result,true);			
		}
		
		/*------------------------------------------------------------------------------------------------*/
		/*		Функция формирования и отправки get-запроса на сервер через cURL						  */
		/*	$href (string) - адрес для подключения (http://gateway.api.sc/rest/)						  */
		/*------------------------------------------------------------------------------------------------*/
		function GetConnect($href)
		{			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $href);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			$result=curl_exec($ch);			
			curl_close($ch);						
			return $result;
		}

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция формирования и отправки post-запроса на сервер через cURL						  */
		/*	$href (string) - адрес для подключения (http://gateway.api.sc/rest/)						  */
		/*	$src (string) - передаваемый запрос															  */
		/*------------------------------------------------------------------------------------------------*/
		function PostConnect($src,$href)
		{		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CRLF, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $src);
			curl_setopt($ch, CURLOPT_URL, $href);
			$result = curl_exec($ch);
			return $result;
			curl_close($ch);
		}

		/*------------------------------------------------------------------------------------------------*/
		/*		Функция изменения даты из unix-формата в формат ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС					  */
		/*	$result (array) - 	массив, в котором значения типа unix-дата будут переведены в формат 	  */
		/*						ГГГГ-ММ-ДД(T)ЧЧ:ММ:СС													  */
		/*------------------------------------------------------------------------------------------------*/
		function ChangeFormateDate($result)
		{
			foreach($result as $key => $value)
			{
				if(is_array($value))
				{
					foreach($value as $key_in => $value_in)
					{
						if(substr($value_in,0,5)=='/Date')
						{
							$value_in = str_replace('/Date(','',$value_in);
							$value_in = str_replace(')/','',$value_in);
							$value_in = substr($value_in,0,strlen($value_in)-3);
							$value_in = date('Y-m-d H:i:s',$value_in);
							$arr_in[$key_in] = $value_in;
						}
						else
							$arr_in[$key_in] = $value_in;
					}
					$arr[$key] = $arr_in;
				}
				else
				{
					if(substr($value,0,5)=='/Date' || $key=='TimeStampUtc')
					{
						$value = str_replace('/Date(','',$value);
						$value = str_replace(')/','',$value);
						$value = substr($value,0,strlen($value)-3);
						$value = date('Y-m-d H:i:s',$value);
						$arr[$key] = $value;
					}
					else
						$arr[$key] = $value;
				}
			}
			return $arr;
		}
		
		function jsonRemoveUnicodeSequences($struct) 
		{
            //SalesPlatform.ru begin
			//return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
            return preg_replace_callback("/\\\\u([a-f0-9]{4})/", function($matches) {
                return iconv('UCS-4LE','UTF-8',pack('V', hexdec('U' . $matches[1])));
            }, json_encode($struct));
            //SalesPlatform.ru end
		}
		
		function JsonArray($arr) 
		  {   
		   $phone_data = array('sms' => $arr);   
		   $result = $this -> jsonRemoveUnicodeSequences($phone_data);
		   return $result;
		  }
 
 }
?>