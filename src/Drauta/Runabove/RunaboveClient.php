<?php namespace Drauta\Runabove;

/*Dependencias*/
use OpenCloud\OpenStack;
use Guzzle\Http\Exception\BadResponseException;


class RunaboveClient{
	private $url = "https://auth.runabove.io/v2.0/";
	private $client;
	private $service;
	private $container;
	public function __construct($client){		
		$this->client = new OpenStack($this->url, array(
		  'username' => $client['username'],
		  'password' => $client['password'],	  
		  'tenantId' => $client['tenantId'],
		));
		/*Esto no se toca de momento*/
		$this->service = $this->client->objectStoreService('swift', 'SBG-1', 'publicURL');		
		$this->container = $this->service->getContainer($client['container']);
	}

	public function fileGet($filename)
	{		
		$object = $this->container->getObject($filename);
		return $object->getUrl();
	}

	public function filePut($file, $filename = null){	
		if($filename == null){
			$filename = $file->getClientOriginalName();
		}	
		$quees = $this->container->uploadObject($filename, fopen($file->getRealPath(), 'r'));				
	}
	
	public function fileExists($filename){	
		foreach($this->container->objectList() as $obj){
			if($obj->getName() == $filename){
				return true;
			}
		}
		return false; 
	}
	
	public function fileList(){
		return $this->container->objectList();
	}
	
	public function fileDelete($filename){
		$object = $this->container->getObject($filename);
		return $object->delete();
	}
	/*Todo crear containers*/
}
