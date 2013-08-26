window.onload = function(){
	var g_username = "Username";
	var g_password = "Password";
	var g_userid = 0;
	var g_pageurl="";//保存用户输入的url
	var copy_file_path = "";//保存hidepage 里的页面地址
	document.getElementById("username").value = g_username;
	document.getElementById("password").value = g_password;
	document.getElementById("takesnap").disabled=false;
	$("#try_btn").click(function(){
		$("#separation").css("display", "none");
		$("#main_page_container").css("display", "none");
		$("#snapshot_page_container").css("display", "block");
		$.ajax({
			type:"POST",
			url:"login.php",
			data:{username: "timeline", password: "timeline"},
			success:function(ms){
				g_userid = ms;
			},
			error:function(ms){
				console.log(ms);
			}
		});
	});
	
	$("#back").click(function(){		
		$("#snapshot_page_container").css("display", "none");
		$("#main_page_container").css("display", "block");
		$("#separation").css("display", "block");
	});
	
	//login
	
	$("#login_btn").click(function(){
		var un = document.getElementById("username").value;
		var psw = document.getElementById("password").value;
		if(!login_verify("username") || !login_verify("password")){
			return;
		}
		$.ajax({
			type:"POST",
			url:"login.php",
			data:{username: un, password: psw},
			success:function(ms){
				g_userid = ms;
			},
			error:function(ms){
				console.log(ms);
			}
		});
		
	});
	
	
	$(".clogin").change(function(e){
		var strid = e.target.id;
		login_verify(strid);
	});
	
	//验证登录的用户名密码是否正确
	function login_verify(strid){
		var $ele = $('#'+strid);
			var bRight = true;
				var usern = /^[a-zA-Z0-9_]{1,}$/;
				var str = document.getElementById(strid).value.trim();
				
				if(str=="" || str==g_username || str==g_password || str.length>12 || str.length<4 ){
					bRight = false;
				}else if(!usern.test(str)){
					bRight = false;
				}
				if(!bRight){
					$ele.focus();
					$ele.addClass('err');
					if(strid=="username"){
						$("#un_right").css("display", "block");
					}else{
						$("#psw_right").css("display", "block");
					}
				}else{
					$ele.removeClass('err');
				}
				return bRight;
	}
	$(".clogin").click(function(e) {
  	this.value = "";
  	if(e.target.id=="username"){
			$("#un_right").css("display", "none");
		}else{
			$("#psw_right").css("display", "none");
		}
});
$("#username").blur(function() {
	if(this.value==""){
  this.value = g_username;
	}
	
});

$("#password").blur(function() {
	if(this.value==""){
  this.value = g_password;
	}
	
});

$("#takesnap").click(function(){
	var pageurl_str = document.getElementById("pageurl").value;
	g_pageurl = pageurl_str;
  $.ajax({
  	type:"POST",
  	url:"preSavefiles.php",
  	data:{pageurl_d: pageurl_str},
  	success:function(msg){
  			copy_file_path = msg;
  			document.getElementById("hidepage").src = copy_file_path;			
				addIFrameEvents();
				
  	}
  	
  });
  
 // $("#content_snp").css("opacity","0.3");
  $("#loading").css("display","block");
  document.getElementById("takesnap").disabled=true;
});

$("#pageurl").click(function(){
	this.value = "";
});

function addIFrameEvents(){

  	var iframe = document.getElementById("hidepage");
  	if(iframe.attachEvent){
  		iframe.attachEvent("onload", function(){
  			//alert("Local iframe is now loaded.");
  			
  		});
  	}else{
  		iframe.onload = function(){
  			
  			var doc = document.getElementById('hidepage').contentDocument;
  			var links_arr = doc.getElementsByTagName("link");
  			
  			var hrefs = new Array();
  			for(var i=0; i<links_arr.length; i++){
  				if(links_arr[i].hasAttribute("href")){
						hrefs.push(links_arr[i].getAttribute("href"));
					}
  			}
  			
  			var scripts_arr = doc.getElementsByTagName("script");
  			var srcs = new Array();
  			for(var i=0; i<scripts_arr.length; i++){
  				if(scripts_arr[i].hasAttribute("src")){
						srcs.push(scripts_arr[i].getAttribute("src"));
					}
  			}
  		
  		
		 $.ajax({
		  	type:"POST",
		  	url:"download.php",
		  	data:{csshref: hrefs, jssrcs: srcs, page: g_pageurl, copyfile:copy_file_path, userid:g_userid},
		  	//dataType: "json",//希望回调函数返回的数据类型
		  	success:function(json){
		  			$("#loading").css("display","none");
		  			$("#snapshot_page_container").css("display","none");
      			$("#header").css("display","block");
      			$("#separation").css("display","block");	
      			$("#main_page_container").css("display","block");
      			$("#show_timeline_page_container").css("display","block");
      			$("#main_content").css("display","none");
      			document.getElementById("takesnap").disabled=false;
		  		}
		  });
  			
  			
  		}
  	}
  }


}