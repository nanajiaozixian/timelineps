window.onload = function(){
	var g_username = "Email";
	var g_password = "Password";
	var g_userid = 0;
	var g_pagerid = 0;
	var g_pageurl="";//保存用户输入的url
	var copy_file_path = "";//保存hidepage 里的页面地址
	var g_blogin = false;
	var g_bsnaping = false;
	var pagefilePath = "";
	document.getElementById("username").value = g_username;
	document.getElementById("password").value = g_password;
	document.getElementById("takesnap").disabled=false;
	var g_pageversion = "pageversion";
	var timeline_init = false;
	login();
	$("#try_btn").click(function(){		
		showSnapshotPage();				
	});
	function login(){
		$.ajax({
			type:"POST",
			url:"login.php",
			data:{username: "timeline", password: "timeline"},
			success:function(ms){
				g_userid = ms;
				g_blogin = true;
				getPages(g_userid);
				showPageTimeline(1);
				
			},
			error:function(ms){
				console.log(ms);
			}
		});
	}
	
	
	//获取所有的页面
	function getPages(userid){
		$.ajax({
  	type:"POST",
  	url:"getAllPages.php",
  	data:{pageid_d: userid},
  	success:function(msg){
  			var pages = eval("("+msg+")");
				if(pages==null){					
					return;
				}
				listpages(pages);
  	}
  	
  });
	}
	
	$("#back").click(function(){		
		/*$("#snapshot_page_container").css("display", "none");
		$("#main_page_container").css("display", "block");
		$("#separation").css("display", "block");
		$("#footer").css("display", "block");*/
		showHomePage();
	});
	$("#show").click(function(){
		showTimelinePage();
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


//sign up
var g_bSignup = false;
$("#signup_btn").click(function(){
	if(g_bSignup == false){
  	$("#signup_info").css("display", "none");
  	$(".c_input_signup").css("display", "block");
  	document.getElementById("email_sign").value = g_username;
		document.getElementById("password_sign").value = g_password;
  	g_bSignup = true;
	}
});

$("#signup_img").click(function(){
	if(g_bSignup == true){
  	
  	$(".c_input_signup").css("display", "none");
  	$("#signup_info").css("display", "block");
  	
  	g_bSignup = false;
	}
});
//snapshot
$("#takesnap").click(function(){
	var pageurl_str = document.getElementById("pageurl").value;
	takeSnapshot(pageurl_str);
});

function takeSnapshot(pagestr){
	
	g_pageurl = pagestr;
  $.ajax({
  	type:"POST",
  	url:"preSavefiles.php",
  	data:{pageurl_d: pagestr},
  	success:function(msg){
  			copy_file_path = msg;
  			document.getElementById("hidepage").src = copy_file_path;			
				addIFrameEvents();
				
  	}
  	
  });
  g_bsnaping = true;
 // $("#content_snp").css("opacity","0.3");
  $("#loading").css("display","block");
  document.getElementById("takesnap").disabled=true;
}

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
		  	success:function(ms){
		  			g_pagerid = ms;
		  			
      			showTimelinePage();
      			g_bsnaping = false;
      			document.getElementById("takesnap").disabled=false;
      			showPageTimeline(g_pagerid);
		  		}
		  });
  		
  			
  		}
  	}
}


function showPageTimeline(pageid_in){
  $.ajax({
  	type:"POST",
  	url:"showTimeline.php",
  	data:{pageid: pageid_in},
  	//dataType: "json",//希望回调函数返回的数据类型
  	success:function(json){
  		getProfile(json);
  		if(g_pageurl==""){
  			getPageInfor(pageid_in);
  		}
  		//console.log(json);
  	}
  });
}

function getProfile(json){
			
			var paths = eval("("+json+")");
			if(paths==null){
				if(!timeline_init){
					timeline_init = true;
				}
				return;
			}
			pagefilePath = paths;
			
			if(paths==null){
				//alert("The page has not any local vertion now. Go to make some snapshots now!");
				return;
			}
			drawTimeline(paths);
			 	
}


function drawTimeline(filesPath){
			
			if(filesPath==""){
				return;
			}
			$("#dates").empty();
				for(var i=0; i<filesPath.length;i++){

				if(filesPath[i]['path']!=null){
						filesPath[i]['path'] = filesPath[i]['path'].replace(/\\/g, "/");
					}
				$("#dates").append(
					'<li><a href="#'+filesPath[i]['version']+'" path="'+filesPath[i]['path']+'">v'+filesPath[i]['version']+'</a></li>');
				
				if(filesPath[i]['information']==null){
					filesPath[i]['information'] = "Nothing have been updated in this version!";
				}
  			$("#issues").append(
  			'<li id='+filesPath[i]['version']+'>'+
  			'<h1>'+filesPath[i]['time']+'</h1>'+
  			'<p>'+filesPath[i]['information']+'</p>'+
  			'</li>');
				
				}
			
			var startpos = filesPath.length/2+1;
			//timline插件
			$(function(){
			$().timelinr({
				orientation: 	'vertical',
				issuesSpeed: 	300,
				datesSpeed: 	100,
				arrowKeys: 		'true',
				startAt:		startpos
			})
		});
	
	
}

function listpages(pages){
	$("#pages").empty();
	for(var i=0; i<pages.length; i++){
		var html = '<li pageid="'+pages[i]['pageid']+'"  title="'+pages[i]['pageurl']+'">'+pages[i]['pageurl']+'</li>';
		//console.log(html);
		$("#pages").append(html);
		if($("#pages li:first-child")){
			var page_selected = $("#pages li:first-child").text();
  		$("#curpage").text(page_selected);
  		$("#curpage").attr("title", page_selected);
  		var pageid = $("#pages li:first-child").attr("pageid");
  		$("#curpage").attr("pageid", pageid);
		}
	}
	$("#pages li").click(function(e){
  	$("#pages li").removeClass("page_selected");
  	$(this).addClass("page_selected");
  	var page_selected = $(this).text();
  	var pageid = $(this).attr("pageid");
  	$("#curpage").text(page_selected);
  	$("#curpage").attr("title", page_selected);
  	$("#curpage").attr("pageid", pageid);
  });
}

$("#navigation").click(function(e){
	switch (e.target.id) {
		case "homepage":
			showHomePage();
			break;
		case "snapshotpage":
			showSnapshotPage();
			break;
		case "showtimelinepage":
			showTimelinePage();
			break;
		default:
			break;
	}
	
});
function showHomePage(){
	$("#loading").css("display","none");
	$("#snapshot_page_container").css("display","none");
	$("#show_timeline_page_container").css("display","none");
	$("#header").css("display","block");
	$("#separation").css("display","block");	
	$("#main_page_container").css("display","block");
	$("#main_content").css("display","block");
	$("#footer").css("display", "block");
      			
}
function showSnapshotPage(){
	if(g_bsnaping == false){
		$("#loading").css("display","none");
	}else{
		$("#loading").css("display","block");
	}
	
	
	$("#show_timeline_page_container").css("display","none");
	$("#header").css("display","none");
	$("#separation").css("display","none");	
	$("#main_page_container").css("display","none");
	$("#main_content").css("display","none");
	$("#footer").css("display", "none");
	$("#snapshot_page_container").css("display","block");
	
      			
}

function showTimelinePage(){
	$("#loading").css("display","none");
	$("#snapshot_page_container").css("display","none");
	$("#main_content").css("display","none");
	$("#show_timeline_page_container").css("display","block");
	$("#header").css("display","block");
	$("#separation").css("display","block");	
	$("#main_page_container").css("display","block");
	$("#footer").css("display", "block");
	if(!timeline_init){
		drawTimeline(pagefilePath);
		timeline_init = true;
	}
	getPages(g_userid);
      			
}

$("#chosepage").mouseover(function(){
	$("#pages").css("display","block");
}).mouseout(function(){
	$("#pages").css("display","none");
});

$("#timeline_show").click(function(){
	var pageid = $("#curpage").attr("pageid");
	showPageTimeline(pageid);
});

$("#timeline_snap").click(function(){
	if(g_bsnaping == true){
		return;
	}
	var curpage = $("#curpage").text();
	$("#pageurl").text(curpage);
	takeSnapshot(curpage);
	showSnapshotPage();
});

function getPageInfor(pageid){
	if(!pageid) return;
	$.ajax({
  	type:"POST",
  	url:"getPageInfor.php",
  	data:{pageid_d: pageid},
  	success:function(msg){
  			var pages = eval("("+msg+")");
				if(pages==null){					
					return;
				}
				if(pages['pageurl']!=null){
					var str = "Page URL: "+pages['pageurl'];
					$("#curpageurl_t").text(str);
				}
				
  	}
  	
  });
}


}