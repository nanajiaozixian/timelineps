<?php
/***
**���ߣ� Doris
**���ڣ� 2013-8-3
**���ã� ������ҳ
**		 ������ҳ���ݶ��ᱣ����pages�ļ����
**       ������������ҳ���ڸ�����ҳ�ļ��е�versions�ļ����XX.html����ҳ�棬����css��js��img�ļ�����others�ļ����
**       XX_local.html��XX.html���޸İ棬�ļ��������·�����ĳ�ָ�򱾵ص��ļ������·��
**		 Ŀǰ���ļ��в㼶�ṹ�ǣ�pages/com.adobe.com/versions/v0/others/
**		 �����Ҫ�޸ı���·���Ĳ㼶�ṹ, ���Կ����޸�VERSIONS�� OTHERS�� V�� $folder_name ��$version�� $others����
***/


/******************************************************************��Ҫ����*******************************************************************************************/

require_once("wrMongodb.php");
require_once("wrMysql.php");
/**�����**/
define('VERSIONS', 'versions');//�������а��ļ����ļ�������
define('OTHERS', 'others');//���������ļ����ļ�������
define('V', 'v');//���浥һ�汾�ļ����ļ�������
define('TEMP', 'temporary');//������ʱ�ļ�
define('CSS', 'css');//����cssԴ�ļ�
define('IMG', 'img');//����cssԴ�ļ�
define('READ_LEN', 4096);
define('BROWSER_SEPARATOR', '/');
 //DIRECTORY_SEPARATOR  ·��'/'  
set_time_limit(300);
$v = 0;//�汾�� 
$url = "";//��ҳurl
$parts = "";
$host = "";
$main_file_init = "";
$folder_name = "";
$folder_name = "";
$version_template = "";
$version = "";
$others = "";
$verpagepath_local = "";
$imgfolder="";
$collection_name="";
$page_id = 0;
$conn = connect_mysql();


function downloadFiles($str_file, $url_in){

global $v;//�汾�� 
global $url ;//��ҳurl
global $parts ;
global $host ;
global $main_file_init ;
global $folder_name ;
global $folder_name ;
global $version_template ;
global $version ;
global $others ;	
global $verpagepath_local;
global $imgfolder;
global $collection_name;//mysql����pagename
global $userid;
global $page_id;

//��ҳurl ��������������������ע�⣬�����ϴ���ʱ���������Ӧ���Ǵ�ǰ�˴����ġ�
$url =  $url_in;
$parts = parse_url($url);//����url
$host = $parts['host'];//��ȡhostname
$main_file_init = basename($parts['path']);//��ȡpathname
$folder_name = preg_replace("/(\w+)\.(\w+)\.(\w+)/i", "$3.$2.$1", $host);
if($main_file_init!==""){
$page_folder = $main_file_init;//��ҳ�����ļ������֣������������壬��www.adobe.com/cn,���ļ�������Ϊcn.com.adobe.com
}else{
	$page_folder = "index";
}
$collection_name = $page_folder.".".$folder_name;
$version_template = "pages".DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$page_folder.DIRECTORY_SEPARATOR.VERSIONS.DIRECTORY_SEPARATOR.V;
$version = $version_template.$v; //version·��: versions\v0 
$others = $version.DIRECTORY_SEPARATOR.OTHERS; //others·��: versions\v0\others
$cssfolder = $version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.CSS; //others·��: versions\v0\others
$imgfolder = $version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.IMG.DIRECTORY_SEPARATOR;

//��ȡpageid
$page_id = getPageId($userid, $collection_name);
$max_ver = getPageVersion($page_id);
$v = ($max_ver==0)? 0: ($max_ver+1);

//��version�ļ���
createFolder($cssfolder);
createFolder($imgfolder);
createFolder(TEMP);
$main_file = $main_file_init;
if($main_file_init===""){
	$main_file = "index.html";
}

if(substr($main_file, -5)!=".html"){
	$main_file = $main_file.".html";
}
$local_file = substr($main_file,0,-5)."_local.html";
//echo "local_file, $local_file  main_file:  $main_file<br/>";
$str = file_get_contents($url);
file_put_contents($version.DIRECTORY_SEPARATOR.$main_file, $str);
$verpagepath_local = $version.DIRECTORY_SEPARATOR.$local_file;// html��local�ļ�����ĵ�ַ
saveFiles($str_file);
//addToDB();//MongoDB
addToMysql();
echo 'true';
}
/********************************************************************���ֺ���************************************************************************************/

/**
*�������� saveFiles
*���ã���str����ȡ���е�css��js��ͼƬ�ļ�·��������
*var: str  ���ҵ�Դ�ļ�
*return: �����ļ���·��
**/
function saveFiles($str){
	$str_new = saveCSSFiles($str);
	$str_new = saveJSFiles($str_new);
	$str_new = saveIMGFiles($str_new);
	$str_new = changeALink($str_new);
	global $verpagepath_local;
	//echo "verpagepath_local: $verpagepath_local<br/>";
	file_put_contents($verpagepath_local, $str_new);
	recursive_delete(TEMP.DIRECTORY_SEPARATOR);//ɾ����ʱ�ļ�������ļ�
}

/**
*�������� createFolder
*���ã��������·��
**/
function createFolder($path)
{
   if (!file_exists($path))
   {
    createFolder(dirname($path));

    mkdir($path, 0777);
   }
}

/**
**��������isFileExist
**���Ҿɰ汾��ĳ�ļ��Ƿ����
**var $filename: �ļ���
**����ֵ  �����򷵻ؾɰ汾�ţ������ڷ���false;
**/
function isFileExist($filename){
	global $version_template;
	global $v;
	$old_v = $v-1;
	for(;$old_v>=0; $old_v--){
		$temppath = $version_template.$old_v.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.CSS.DIRECTORY_SEPARATOR.$filename;
		
		if(file_exists($temppath)){
			return $old_v;
		}
	}
	return false;
}


/**
**��������saveCSSFiles
**�洢css�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveCSSFiles($str){
	global $url;
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;//BROWSER_SEPARATOR;
	$arr_link_css = array(); //����css �ļ�����link
	$arr_filename_css = array(); //����css �ļ�������
	$arr_localpath_css = array();//����css �ļ����ش洢·��
		//file_put_contents("temp.html", $str);
	preg_match_all("/<link\s+.*?href=[\"|'](.+?)[\"|'].*?>/",$str,$links, PREG_SET_ORDER);//links �ﱣ���˴�ҳ���ȡ������css�ļ���·��
	$count = 0;	
	//var_dump($links);
	foreach($links as $val){	
		if(strpos($val[1], "http:")!==0 && substr($val[1], 0,1)!=="/"){		
			continue;
		}
		$arr_link_css[$count] = $val[1];
		/*if(strpos($val[1], "http:")!==0){
			
			$val[1] = $links[$count][1] = "http://".$host.$val[1];
		}	*/
		$parts_css = parse_url($val[1]);
		if(!array_key_exists('path',$parts_css)){
			continue;
		}
		$filname_css = basename($parts_css['path']);//��ȡpathname
		if($filname_css===""){
			continue;
		}
		$filname_css = ifFileNameRepeat($filname_css, $arr_filename_css);
		$arr_filename_css[$count] = $filname_css;
		//�ж�������Ч��
		//echo $val[1]."<br/>";
		if(get_headers($val[1])!==false){		
				$str_file_content = file_get_contents($val[1]);
				
    		$newfilepath = $version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.CSS.DIRECTORY_SEPARATOR.$filname_css;
    		$newlocalfilepath = $version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_css;
    		$arr_localpath_css[$count] = OTHERS.BROWSER_SEPARATOR.$filname_css;
    		
    		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
    		$old_version = isFileExist($filname_css);
    		$oldfilepath = "";
    		if($old_version === false){ 
    			//echo "newfilepath:  $newfilepath \n\r  str_file_content: $str_file_content";		
    			file_put_contents($newfilepath, $str_file_content);
    			$str_localfile_content = relative_to_absolute($str_file_content, $url);
    			$str_localfile_content = saveFilesInCss($str_localfile_content);
    			//echo "newlocalfilepath:  $newlocalfilepath \n\r  str_localfile_content: $str_localfile_content";	
    			file_put_contents($newlocalfilepath, $str_localfile_content);
    		}else{
    			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.CSS.DIRECTORY_SEPARATOR.$filname_css;
    			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_css;
    			file_put_contents($tempfilepath, $str_file_content);
    			if(!compare($oldfilepath, $tempfilepath)){
    				file_put_contents($newfilepath, $str_file_content);
    				$str_localfile_content = relative_to_absolute($str_file_content, $url);
    				$str_localfile_content = saveFilesInCss($str_localfile_content);
    				file_put_contents($newlocalfilepath, $str_localfile_content);
    			}else{
    				$arr_localpath_css[$count] = "..".BROWSER_SEPARATOR.V.$old_version.BROWSER_SEPARATOR.OTHERS.BROWSER_SEPARATOR.$filname_css;
    				
    			}
    		}
		}
		
		$count++;
	}
	
	//��html�ļ����css·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_css, $arr_localpath_css, $str_new);
	return $str_new;
}

/**
**��������saveJSFiles
**�洢js�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveJSFiles($str){
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.BROWSER_SEPARATOR;
	$arr_link_js = array(); //����js �ļ�����link
	$arr_filename_js = array(); //����js �ļ�������
	$arr_localpath_js = array();//����js �ļ����ش洢·��
	$count = 0;	


	preg_match_all("/<script\s+.*?src=[\"|']([^\"']*)[\"|'].*?>/",$str,$scripts, PREG_SET_ORDER);//scripts �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	//�洢js�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	//var_dump($scripts);
	foreach($scripts as $val){	
		if(strpos($val[1], "http:")!==0 && substr($val[1], 0,1)!=="/"){		
			continue;
		}
		$arr_link_js[$count] = $val[1];
		/*if(strpos($val[1], "http:")!==0){
			
			$val[1] = $scripts[$count][1] = "http://".$host.$val[1];
		}	*/
		$parts_js = parse_url($val[1]);
		
		if(!array_key_exists('path',$parts_js)){
			continue;
		}
		$filname_js = basename($parts_js['path']);//��ȡpathname
		$arr_filename_js[$count] = $filname_js;
		//�ж�������Ч��
		if(get_headers($val[1])!==false){		
				//echo $val[1].'<br/>';
				$str_file_content = file_get_contents($val[1]);
    		$newfilepath = $version.DIRECTORY_SEPARATOR.$localpath.$filname_js;
    		$arr_localpath_js[$count] = $localpath.$filname_js;
    
    		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
    		$old_version = isFileExist($filname_js);	
    		$oldfilepath = "";
    		if($old_version === false){
    			file_put_contents($newfilepath, $str_file_content);
    		}else{
    			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_js;
    			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_js;
    			file_put_contents($tempfilepath, $str_file_content);
    			if(!compare($oldfilepath, $tempfilepath)){
    				file_put_contents($newfilepath, $str_file_content);
    			}else{
    				$arr_localpath_js[$count] = "..".BROWSER_SEPARATOR.V.$old_version.BROWSER_SEPARATOR.OTHERS.BROWSER_SEPARATOR.$filname_js;
    				
    			}
    		}
		}
		
		$count++;
	}

	//��html�ļ����js·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_js, $arr_localpath_js, $str_new);
	return $str_new;
}


/**
**��������saveIMGFiles
**�洢img�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveIMGFiles($str){
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.BROWSER_SEPARATOR;
	$arr_link_img = array(); //����img �ļ�����link
	$arr_filename_img = array(); //����img �ļ�������
	$arr_localpath_img = array();//����img �ļ����ش洢·��
	$count = 0;	

	preg_match_all("/<img\s+.*?src=[\"|']([^\"']*)[\"|'].*?>/",$str,$images, PREG_SET_ORDER);//images �ﱣ���˴�ҳ���ȡ������img�ļ���·��
	//�洢img�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��

	//var_dump($images);
	foreach($images as $val){	
		if(strpos($val[1], "http:")!==0 && substr($val[1], 0,1)!=="/"){		
			continue;
		}

		$arr_link_img[$count] = $val[1];
		if(strpos($val[1], "http:")!==0 && substr($val[1], 0,1)=="/"){	
			array_push($arr_link_img, $val[1]);	
			//$val[1] = $images[$count][1] = "http://".$host.$val[1];
		}	
	
		$parts_img = parse_url($val[1]);
		if(!array_key_exists('path',$parts_img)){
			continue;
		}
	
		$filname_img = basename($parts_img['path']);//��ȡpathname
		if($filname_img===""){
			continue;
		}
		$arr_filename_img[$count] = $filname_img;
		//�ж�������Ч��
		if(get_headers($val[1])!==false){		
				//echo $val[1].'<br/>';
				$str_file_content = file_get_contents($val[1]);
    		$newfilepath = $version.DIRECTORY_SEPARATOR.$localpath.$filname_img;
    		$arr_localpath_img[$count] = $localpath.$filname_img;
    
    		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
    		$old_version = isFileExist($filname_img);
    	
    		$oldfilepath = "";
    		if($old_version === false){
    			file_put_contents($newfilepath, $str_file_content);
    			
    		}else{	
    			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_img;
    			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_img;
    			file_put_contents($tempfilepath, $str_file_content);
    			if(!compare($oldfilepath, $tempfilepath)){
    				file_put_contents($newfilepath, $str_file_content);
    			}else{
    				$arr_localpath_img[$count] = "..".BROWSER_SEPARATOR.V.$old_version.BROWSER_SEPARATOR.OTHERS.BROWSER_SEPARATOR.$filname_img;
    				
    			}
    		}
		}
		
		$count++;
	}
	//��html�ļ����img·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_img, $arr_localpath_img, $str_new);
	return $str_new;
}

/**
**��������recursive_delete
**ɾ���ļ��������е��ļ�
**var $dir: �ļ���·��
**/
function recursive_delete($dir)
{
	if(is_dir($dir)){
	   if($dh = opendir($dir)){
		   while(($file = readdir($dh)) !== false ){
				if($file != "." && $file != "..")
				{
					if(is_dir($dir.$file))
					{                               
					  recursive_delete($dir.$file."/"); 
					  rmdir($dir.$file );
					}
					else
					{
					  unlink( $dir.$file);
					}
				}
		   }
		   closedir($dh);
	   }
	}
}

/**
**�������� compare
**���ã� �Ա������ļ�
**var file1:�ļ�1��·��  file2: �ļ�2��·��
**�ο����ף�http://www.php.net/manual/zh/function.md5-file.php
**          
**/


function compare($file1, $file2){
	return files_identical($file1, $file2);
}

function files_identical($fn1, $fn2) {
    if(filetype($fn1) !== filetype($fn2))
        return FALSE;

    if(filesize($fn1) !== filesize($fn2))
        return FALSE;

    if(!$fp1 = fopen($fn1, 'rb'))
        return FALSE;

    if(!$fp2 = fopen($fn2, 'rb')) {
        fclose($fp1);
        return FALSE;
    }

    $same = TRUE;
    while (!feof($fp1) and !feof($fp2))
        if(fread($fp1, READ_LEN) !== fread($fp2, READ_LEN)) {
            $same = FALSE;
            break;
        }

    if(feof($fp1) !== feof($fp2))
        $same = FALSE;

    fclose($fp1);
    fclose($fp2);

    return $same;
}

//�޸�a��ǩ��link�� ��������·�������Ϊ����·����
function changeALink($str){
	global $parts;
	global $host;
	$absolute_path = $parts['scheme']."://".$host;	
	$str_new = $str;
	$pattern = "/(<a\s+.*?href=[\"|'])(\/[^\"\']*)([\"|'].*?>)/";//ע�������õ��ġ��������˴�Ҫ�÷�̰��ģʽ
	$replacement = '${1}'.$absolute_path.'$2$3';
	$str_new = preg_replace($pattern, $replacement, $str_new);
	return $str_new;
}

function addToDB(){
	global $v;
	global $verpagepath_local;
	global $collection_name;
	
	$ver_arr = array(V.$v=>$verpagepath_local);
//var_dump($ver_arr);
	addNewVersion($collection_name, $ver_arr);
}

function addToMysql(){
	global $verpagepath_local;
	global $collection_name;
	global $v;
	$path = preg_replace("/\\\\/","\\\\\\",$verpagepath_local);
	date_default_timezone_set('America/Los_Angeles');
	$today = date("Y-m-d H:i:s");
	$query = "insert into pages values (NULL, '".$v."', '".$today."', '".$path."', NULL, '".$page_id."')";
	$result = mysql_query($query);
	if(!$result){
		die("Insert failure!".mysql_error());
	}
}

//����css�ļ������õ������ļ�
function saveFilesInCss($str_file){
	$urls_arr = array();
	$local_urls_arr = array();
	global $imgfolder;
	$savepath = $imgfolder;
	$localpath = IMG.BROWSER_SEPARATOR;
	preg_match_all('/url\((["|\']?)(.*?)\\1\)/',$str_file,$links, PREG_SET_ORDER);//links �ﱣ���˴�ҳ���ȡ������css�ļ���·��
	array_unique($links);
	foreach($links as $val){	
		$val[2] = trim($val[2]);	
		if(substr($val[2],0, 5)==="data:"){
			continue;
		}
		//echo $val[2];
		array_push($urls_arr,$val[2]);
		$parts_img = parse_url($val[2]);
		if(!array_key_exists('path',$parts_img)){
			continue;
		}
	
		$filname_img = basename($parts_img['path']);//��ȡpathname
		if($filname_img===""){
			continue;
		}
		$str = file_get_contents($val[2]);
		
		array_push($local_urls_arr, $localpath.$filname_img);
		
		file_put_contents( $savepath.$filname_img, $str);
		
	}
	$str_new = str_replace($urls_arr, $local_urls_arr, $str_file);
	//var_dump($local_urls_arr);
	return $str_new;
	
}

function ifFileNameRepeat($filename, $name_arr, $num=1){
	if(in_array($filename, $name_arr)===TRUE){			
			$filename = substr($filename, 0, -4)."_".$num.substr($filename,-4);
			$num++;
			return ifFileNameRepeat($filename, $name_arr,$num);
		}else{
			return $filename;
		}
}
?>