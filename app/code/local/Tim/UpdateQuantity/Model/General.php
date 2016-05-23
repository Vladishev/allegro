<?php

/**
 * Class Tim_UpdateQuantity_Model_General
 */
class Tim_UpdateQuantity_Model_General
{
    /**
     * @var array System emails recipients
     */
	protected $recipients = array(
		array('email' => 'j.kiernicki@tim.pl', 'name' => 'Jacek Kiernicki'),
		array('email' => 'm.buczak@tim.pl', 'name' => 'Marcin Buczak'),
		array('email' => 'k.drobnik@tim.pl', 'name' => 'Krzysztof Drobnik'),
	);

    /**
     * @var array Local data base connection info
     */
	protected $multistoreDbInfo;
    /**
     * @var array External data base connection info
     */
	protected $wmsDbInfo;

    /**
     * Sends system emails
     *
     * @param string $subject
     * @param string $text
     * @param string $message
     */
	public function mail($subject,$text,$message)
	{
		foreach($this->recipients as $recipient){
			$headers = "Reply-to: no reply <no_reply@przewody.pl>".PHP_EOL;
			$headers .= "From: Multistore <sklep@przewody.pl>".PHP_EOL;
			$headers .= "MIME-Version: 1.0".PHP_EOL;
			$headers .= "Content-type: text/html; charset=utf-8".PHP_EOL; 
			
				$message = "<html> 
				<head> 
				   <title>$subject</title> 
				</head>
				<body>
					<p>
						<b>$text</b>
					</p>
				   <p>$message</p>
				</body>
				</html>";

			mail($recipient['email'],$subject,$message,$headers);
		}
	}

    /**
     * Returns local db access
     *
     * @return array
     */
	public function getMultistoreAccess()
	{
		if($this->multistoreDbInfo){
			return $this->multistoreDbInfo;
		}
		

		$config  = Mage::app()->getConfig()->getResourceConnectionConfig("default_setup");

		return $this->multistoreDbInfo = array("host" => $config->host,
					"user" => $config->username,
					"pass" => $config->password,
					"dbname" => $config->dbname
				);
	}

    /**
     * Returns external db access
     *
     * @return array|mixed
     */
	public function getWmsAccess()
	{
		if($this->wmsDbInfo){
			return $this->wmsDbInfo;
		}
		
		$this->wmsDbInfo = Mage::getStoreConfig('tim_wms/tim_wms_group');

		if(!$this->wmsDbInfo){
			$this->mail('Błąd Mssql!','Błąd połączenia z WMS','Sprawdź ustawienia w Konfiguracja->Tim SA->WMS connect');
		}
		return $this->wmsDbInfo;
	}
}