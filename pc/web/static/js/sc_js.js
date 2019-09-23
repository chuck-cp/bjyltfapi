$(function (){
	var pheight=$(document).height()+100;      
	var wdheight=$(".yx_right_menu").height();   
	if(pheight>=wdheight){
		$(".yx_left_menu").height(pheight-60);	
		$(".yx_right_menu").height(pheight);
	}else{
            $(".yx_left_menu").height(wdheight)           
        }	
})