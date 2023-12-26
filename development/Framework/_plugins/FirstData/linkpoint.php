<?php
namespace ClickBlocks\Utils;

class LinkPoint
{
	public $debugging = 0;
	protected $proxy = false;

	public function setProxy($proxy)
	{
		$this->proxy = $proxy;
	}

	public function process($data)
	{
		$using_xml = 0;
		$webspace = 1;
		$echo = '';

		if (isset($data["webspace"]))
		{
			if ($data["webspace"] == "false") // if explicitly set to false, don't use html output
				$webspace = 0;
		}

		if($data['debug'])
		{
			$this->debugging = 1;

			$echo .= "at curl_process, incoming data: \n";
			
			while (list($key, $value) = each($data))
				$echo .= "$key = $value\n";

			reset($data); 
		}

		if (isset($data["xml"])) // if XML string is passed in, we'll use it
		{
			$using_xml = 1;
			$xml = $data["xml"];
		}
		else
		{
			// otherwise convert incoming hash to XML string
			$xml = $this->buildXML($data);
		}

		if ($this->debugging)
				$echo .= "\nsending xml string:\n$xml\n\n";

		// set up transaction variables
		$key = $data["keyfile"];
		$port = $data["port"];
		$host = "https://".$data["host"].":".$port."/LSGSXML";

		$ch = curl_init ();
			if($this->proxy)
			{
				echo "Connect to $host through {$this->proxy}...<br/>";
				// curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);				
				curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, 1);				
				curl_setopt ($ch, CURLOPT_PROXY, $this->proxy); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);			
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			}
			curl_setopt ($ch, CURLOPT_URL,$host);
			curl_setopt ($ch, CURLOPT_POST, 1); 
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt ($ch, CURLOPT_SSLCERT, $key);
			curl_setopt ($ch, CURLOPT_SSLCERTTYPE,   'PEM');
			if(!is_file($key))
				print_p("$key not found");
			// curl_setopt ($ch, CURLOPT_SSLCERTPASSWD, '111111111');

			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

			if ($this->debugging)
				curl_setopt ($ch, CURLOPT_VERBOSE, 1);

			#  use curl to send the xml SSL string
			$result = curl_exec ($ch);
			// print_p(curl_getinfo($ch));

			if($result === false)
			{
				$result = curl_error($ch);
				return $result;
			}

			curl_close($ch);


			if ($this->debugging)
			{	
				$echo .= "\nserver responds:\n $result\n\n";

				if($webspace) $echo = nl2br(htmlspecialchars($echo));
				echo $echo;
			}

			if ($using_xml)
			{
				# send xml response back
				return $result;
			}
			else
			{
				#convert xml response to hash
				$retarr = $this->decodeXML($result);
				
				# and send it back
				return ($retarr);
			}
		}


	#############################################	
	#
	#	F U N C T I O N   d e c o d e X M L ( ) 
	#
	#	converts the LSGS response xml string	
	#	to a hash of name-value pairs
	#
	#############################################

	function decodeXML($xmlstg)
	{
		preg_match_all ("/<(.*?)>(.*?)\</", $xmlstg, $out, PREG_SET_ORDER);
		
		$n = 0;
		while (isset($out[$n]))
		{
			$retarr[$out[$n][1]] = strip_tags($out[$n][0]);
			$n++; 
		}

		return $retarr;
	}


	############################################
	#
	#	F U N C T I O N    b u i l d X M L ( ) 
	#
	#	converts a hash of name-value pairs
	#	to the correct XML format for LSGS
	#
	############################################

	function buildXML($pdata)
	{

		// while (list($key, $value) = each($pdata))
		// 	 echo htmlspecialchars($key) . " = " . htmlspecialchars($value) . "<br>\n";

		### ORDEROPTIONS NODE ###
		$xml = "<order><orderoptions>";

		if (isset($pdata["ordertype"]))
			$xml .= "<ordertype>" . $pdata["ordertype"] . "</ordertype>";

		if (isset($pdata["result"]))
			$xml .= "<result>" . $pdata["result"] . "</result>";

		$xml .= "</orderoptions>";


		### CREDITCARD NODE ###
		$xml .= "<creditcard>";

		if (isset($pdata["cardnumber"]))
			$xml .= "<cardnumber>" . $pdata["cardnumber"] . "</cardnumber>";

		if (isset($pdata["cardexpmonth"]))
			$xml .= "<cardexpmonth>" . $pdata["cardexpmonth"] . "</cardexpmonth>";

		if (isset($pdata["cardexpyear"]))
			$xml .= "<cardexpyear>" . $pdata["cardexpyear"] . "</cardexpyear>";

		if (isset($pdata["cvmvalue"]))
			$xml .= "<cvmvalue>" . $pdata["cvmvalue"] . "</cvmvalue>";

		if (isset($pdata["cvmindicator"]))
			$xml .= "<cvmindicator>" . $pdata["cvmindicator"] . "</cvmindicator>";

		if (isset($pdata["track"]))
			$xml .= "<track>" . $pdata["track"] . "</track>";

		$xml .= "</creditcard>";


		### BILLING NODE ###
		$xml .= "<billing>";

		if (isset($pdata["name"]))
			$xml .= "<name>" . $pdata["name"] . "</name>";

		if (isset($pdata["company"]))
			$xml .= "<company>" . $pdata["company"] . "</company>";

		if (isset($pdata["address1"]))
			$xml .= "<address1>" . $pdata["address1"] . "</address1>";
		elseif (isset($pdata["address"]))
			$xml .= "<address1>" . $pdata["address"] . "</address1>";

		if (isset($pdata["address2"]))
			$xml .= "<address2>" . $pdata["address2"] . "</address2>";

		if (isset($pdata["city"]))
			$xml .= "<city>" . $pdata["city"] . "</city>";
			
		if (isset($pdata["state"]))
			$xml .= "<state>" . $pdata["state"] . "</state>";
			
		if (isset($pdata["zip"]))
			$xml .= "<zip>" . $pdata["zip"] . "</zip>";

		if (isset($pdata["country"]))
			$xml .= "<country>" . $pdata["country"] . "</country>";

		if (isset($pdata["userid"]))
			$xml .= "<userid>" . $pdata["userid"] . "</userid>";

		if (isset($pdata["email"]))
			$xml .= "<email>" . $pdata["email"] . "</email>";

		if (isset($pdata["phone"]))
			$xml .= "<phone>" . $pdata["phone"] . "</phone>";

		if (isset($pdata["fax"]))
			$xml .= "<fax>" . $pdata["fax"] . "</fax>";

		if (isset($pdata["addrnum"]))
			$xml .= "<addrnum>" . $pdata["addrnum"] . "</addrnum>";

		$xml .= "</billing>";

		
		## SHIPPING NODE ##
		$xml .= "<shipping>";

		if (isset($pdata["sname"]))
			$xml .= "<name>" . $pdata["sname"] . "</name>";

		if (isset($pdata["saddress1"]))
			$xml .= "<address1>" . $pdata["saddress1"] . "</address1>";

		if (isset($pdata["saddress2"]))
			$xml .= "<address2>" . $pdata["saddress2"] . "</address2>";

		if (isset($pdata["scity"]))
			$xml .= "<city>" . $pdata["scity"] . "</city>";

		if (isset($pdata["sstate"]))
			$xml .= "<state>" . $pdata["sstate"] . "</state>";
		elseif (isset($pdata["state"]))
			$xml .= "<state>" . $pdata["sstate"] . "</state>";

		if (isset($pdata["szip"]))
			$xml .= "<zip>" . $pdata["szip"] . "</zip>";
		elseif (isset($pdata["sip"]))
			$xml .= "<zip>" . $pdata["zip"] . "</zip>";

		if (isset($pdata["scountry"]))
			$xml .= "<country>" . $pdata["scountry"] . "</country>";

		if (isset($pdata["scarrier"]))
			$xml .= "<carrier>" . $pdata["scarrier"] . "</carrier>";

		if (isset($pdata["sitems"]))
			$xml .= "<items>" . $pdata["sitems"] . "</items>";

		if (isset($pdata["sweight"]))
			$xml .= "<weight>" . $pdata["sweight"] . "</weight>";

		if (isset($pdata["stotal"]))
			$xml .= "<total>" . $pdata["stotal"] . "</total>";

		$xml .= "</shipping>";


		### TRANSACTIONDETAILS NODE ###
		$xml .= "<transactiondetails>";

		if (isset($pdata["oid"]))
			$xml .= "<oid>" . $pdata["oid"] . "</oid>";

		if (isset($pdata["ponumber"]))
			$xml .= "<ponumber>" . $pdata["ponumber"] . "</ponumber>";

		if (isset($pdata["recurring"]))
			$xml .= "<recurring>" . $pdata["recurring"] . "</recurring>";

		if (isset($pdata["taxexempt"]))
			$xml .= "<taxexempt>" . $pdata["taxexempt"] . "</taxexempt>";

		if (isset($pdata["terminaltype"]))
			$xml .= "<terminaltype>" . $pdata["terminaltype"] . "</terminaltype>";

		if (isset($pdata["ip"]))
			$xml .= "<ip>" . $pdata["ip"] . "</ip>";

		if (isset($pdata["reference_number"]))
			$xml .= "<reference_number>" . $pdata["reference_number"] . "</reference_number>";

		if (isset($pdata["transactionorigin"]))
			$xml .= "<transactionorigin>" . $pdata["transactionorigin"] . "</transactionorigin>";

		if (isset($pdata["tdate"]))
			$xml .= "<tdate>" . $pdata["tdate"] . "</tdate>";

		$xml .= "</transactiondetails>";


		### MERCHANTINFO NODE ###
		$xml .= "<merchantinfo>";

		if (isset($pdata["configfile"]))
			$xml .= "<configfile>" . $pdata["configfile"] . "</configfile>";

		if (isset($pdata["keyfile"]))
			$xml .= "<keyfile>" . $pdata["keyfile"] . "</keyfile>";

		if (isset($pdata["host"]))
			$xml .= "<host>" . $pdata["host"] . "</host>";

		if (isset($pdata["port"]))
			$xml .= "<port>" . $pdata["port"] . "</port>";

		if (isset($pdata["appname"]))
			$xml .= "<appname>" . $pdata["appname"] . "</appname>";

		$xml .= "</merchantinfo>";



		### PAYMENT NODE ###
		$xml .= "<payment>";

		if (isset($pdata["chargetotal"]))
			$xml .= "<chargetotal>" . $pdata["chargetotal"] . "</chargetotal>";

		if (isset($pdata["tax"]))
			$xml .= "<tax>" . $pdata["tax"] . "</tax>";

		if (isset($pdata["vattax"]))
			$xml .= "<vattax>" . $pdata["vattax"] . "</vattax>";

		if (isset($pdata["shipping"]))
			$xml .= "<shipping>" . $pdata["shipping"] . "</shipping>";

		if (isset($pdata["subtotal"]))
			$xml .= "<subtotal>" . $pdata["subtotal"] . "</subtotal>";

		$xml .= "</payment>";


		### CHECK NODE ### 


		if (isset($pdata["voidcheck"]))
		{
			$xml .= "<telecheck><void>1</void></telecheck>";
		}
		elseif (isset($pdata["routing"]))
		{
			$xml .= "<telecheck>";
			$xml .= "<routing>" . $pdata["routing"] . "</routing>";

			if (isset($pdata["account"]))
				$xml .= "<account>" . $pdata["account"] . "</account>";

			if (isset($pdata["bankname"]))
				$xml .= "<bankname>" . $pdata["bankname"] . "</bankname>";
	
			if (isset($pdata["bankstate"]))
				$xml .= "<bankstate>" . $pdata["bankstate"] . "</bankstate>";

			if (isset($pdata["ssn"]))
				$xml .= "<ssn>" . $pdata["ssn"] . "</ssn>";

			if (isset($pdata["dl"]))
				$xml .= "<dl>" . $pdata["dl"] . "</dl>";

			if (isset($pdata["dlstate"]))
				$xml .= "<dlstate>" . $pdata["dlstate"] . "</dlstate>";

			if (isset($pdata["checknumber"]))
				$xml .= "<checknumber>" . $pdata["checknumber"] . "</checknumber>";
				
			if (isset($pdata["accounttype"]))
				$xml .= "<accounttype>" . $pdata["accounttype"] . "</accounttype>";

			$xml .= "</telecheck>";
		}


		### PERIODIC NODE ###

		if (isset($pdata["startdate"]))
		{
			$xml .= "<periodic>";

			$xml .= "<startdate>" . $pdata["startdate"] . "</startdate>";

			if (isset($pdata["installments"]))
				$xml .= "<installments>" . $pdata["installments"] . "</installments>";

			if (isset($pdata["threshold"]))
						$xml .= "<threshold>" . $pdata["threshold"] . "</threshold>";

			if (isset($pdata["periodicity"]))
						$xml .= "<periodicity>" . $pdata["periodicity"] . "</periodicity>";

			if (isset($pdata["pbcomments"]))
						$xml .= "<comments>" . $pdata["pbcomments"] . "</comments>";

			if (isset($pdata["action"]))
				$xml .= "<action>" . $pdata["action"] . "</action>";

			$xml .= "</periodic>";
		}


		### NOTES NODE ###

		if (isset($pdata["comments"]) || isset($pdata["referred"]))
		{
			$xml .= "<notes>";

			if (isset($pdata["comments"]))
				$xml .= "<comments>" . $pdata["comments"] . "</comments>";

			if (isset($pdata["referred"]))
				$xml .= "<referred>" . $pdata["referred"] . "</referred>";

			$xml .= "</notes>";
		}

		### ITEMS AND OPTIONS NODES ###
	
			reset($pdata);
			$tab = $this->debugging ? "\t" : "";
			$nl  = $this->debugging ? "\n" : "";
			while (list ($key, $val) = each ($pdata))
			{
				if (is_array($val))
				{
					$otag = 0;
					$ostag = 0;
					$items_array = $val;
					$xml .= "{$nl}<items>{$nl}";

					while(list($key1, $val1) = each ($items_array))
					{
						$xml .= "{$tab}<item>{$nl}";

						while (list($key2, $val2) = each ($val1))
						{
							if (!is_array($val2))
								$xml .= "{$tab}{$tab}<$key2>$val2</$key2>{$nl}";

							else
							{
								if (!$ostag)
								{
									$xml .= "{$tab}{$tab}<options>{$nl}";
									$ostag = 1;
								}

								$xml .= "{$tab}{$tab}{$tab}<option>{$nl}";
								$otag = 1;
								
								while (list($key3, $val3) = each ($val2))
									$xml .= "{$tab}{$tab}{$tab}{$tab}<$key3>$val3</$key3>{$nl}";
							}

							if ($otag)
							{
								$xml .= "{$tab}{$tab}{$tab}</option>{$nl}";
								$otag = 0;
							}
						}

						if ($ostag)
						{
							$xml .= "{$tab}{$tab}</options>{$nl}";
							$ostag = 0;
						}
					$xml .= "{$tab}</item>{$nl}";
					}
				$xml .= "</items>{$nl}";
				}
			}
		$xml .= "</order>";

		return $xml;
	}
}
?>