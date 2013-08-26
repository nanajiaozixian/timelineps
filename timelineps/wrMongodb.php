<?php
/*$collection_name = "cn.com.adobe.www";
$vers_arr = array("v1"=>"aaaa");
addNewVersion($collection_name,$vers_arr);*/

//新建mongodbclient
function newMongoClient(){
	global $mongo;
	$mongo = new MongoClient();
}

//将新版本的地址保存到数据库
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

//检查将要存入的版本在数据库里是否已经有记录
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

//获取网页的所有版本信息
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
  
  //$pagehost_collection->remove();//清空collection
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