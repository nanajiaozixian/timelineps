<?php
/*$collection_name = "cn.com.adobe.www";
$vers_arr = array("v1"=>"aaaa");
addNewVersion($collection_name,$vers_arr);*/

//�½�mongodbclient
function newMongoClient(){
	global $mongo;
	$mongo = new MongoClient();
}

//���°汾�ĵ�ַ���浽���ݿ�
function addNewVersion($collection_name,$vers_arr){
	global $mongo;
	if($mongo==null){
		$mongo = new MongoClient();
	}
	try{
    	$war_db = $mongo->selectDB("pages");
   }catch(Exception $e){
    	$war_db = new MongoDB($mongo, "pages");
   }
        
  try{
  	$pagehost_collection = $war_db->selectCollection($collection_name);
  }catch(Exception $e){
  	$pagehost_collection = new createCollection($collection_name);
  }
  $keys = array_keys($vers_arr);
  $r = checkIfVertionExist($pagehost_collection, $keys[0]);
  if($r===false){
		$rs = $pagehost_collection->insert($vers_arr);	
	}else{
		$pagehost_collection->update($r, $vers_arr);
	}
}

//��齫Ҫ����İ汾�����ݿ����Ƿ��Ѿ��м�¼
function checkIfVertionExist($collection_name,$ver){
	$index = $collection_name->find();
 
   while($index->hasNext()){
    	$ii = $index->getNext();
    	while(list($key, $val)=each($ii)){
    	
    		if($key === $ver){
    			return array($key=>$val);
    		}
  		}
  }
  
  return false;
}

//��ȡ��ҳ�����а汾��Ϣ
function getMyPageCollect($pagehostname){
	global $mongo;
	if($mongo==null){
		$mongo = new MongoClient();
	}
	try{
    	$war_db = $mongo->selectDB("pages");
   }catch(Exception $e){
    	$war_db = new MongoDB($mongo, "pages");
   }
        
  try{
  	$pagehost_collection = $war_db->selectCollection($pagehostname);
  }catch(Exception $e){
  	//$pagehost_collection = new createCollection($pagehostname);
  	return false;
  }
  
  //$pagehost_collection->remove();//���collection
  $index = $pagehost_collection->find();
  $infor = array();
 
 while($index->hasNext()){
  	$ii = $index->getNext();
  	while(list($key, $val)=each($ii)){
  	
  		$l = array($key=>$val);
  		if($key=="_id"){
  			continue;
  		}
  		
  		//var_dump($l);
  		array_push($infor, $l);
		}
	}
	
	return $infor;
}

?>