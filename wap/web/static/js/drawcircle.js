function drawcircle(el,deg,lineWidth,lineBgColor,lineColor,textColor,fontSize,circleRadius,opentime,timeval){
	var cvsElement=document.getElementById('my_html');
	var ctx=cvsElement.getContext('2d');   
    ctx.lineWidth=5
    ctx.strokeStyle="aqua"
    ctx.arc(50,50,40,0,1.5*Math.PI)
    ctx.stroke()  	
    width = cvsElement.width,//元素宽度
    height=cvsElement.height,//元素高度
    degActive=0,//动态线条
    timer=null;//定时器    	
     	
   //停止时的角度
    deg>0&&deg<=100?deg:deg = 100;
    
    //线宽
    lineWidth !== undefined?lineWidth :lineWidth =20;

    //判断宽高较小者
    var wh = width>height?height:width;
    //设置圆的半径，默认为宽高较小者
    circleRadius>0&&circleRadius<=wh/2-lineWidth/2?circleRadius =circleRadius:circleRadius =wh/2-lineWidth/2;

    //绘制线的背景颜色
    lineBgColor !==undefined?lineBgColor:lineBgColor='#ccc';

    //绘制线的颜色
    lineColor !==undefined?lineColor:lineColor='#009ee5';

    //绘制文字颜色
    textColor !==undefined?textColor:textColor='#009ee5';
    //绘制文字大小
    fontSize !==undefined?fontSize :fontSize=parseInt(circleRadius/2);
    //执行时间
 
   //清除锯齿
    if (window.devicePixelRatio) {
        cvsElement.style.width = width + "px";
        cvsElement.style.height = height + "px";
        cvsElement.height = height * window.devicePixelRatio;
        cvsElement.width = width * window.devicePixelRatio;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    }
    //设置线宽
    ctx.lineWidth=lineWidth;
        //启动定时器
        timer = setInterval(function(){
        	//实时获取当前时间
        	var timestamp = Date.parse(new Date())/1000;
        	//获取时间差
        	var countdown=opentime-timestamp;        	
            ctx.clearRect(0,0,width,height);//清除画布
            ctx.beginPath();//开始绘制底圆
            ctx.arc(width/2,height/2,circleRadius,1,8);
            ctx.strokeStyle=lineBgColor;
            ctx.stroke();  
            ctx.beginPath();//开始绘制动态圆  
			if (countdown <= 0) { 				 
		   	  	 ctx.arc(width/2,height/2,circleRadius,0,360);
		   	  	 ctx.strokeStyle="green"; 
		   	  	 ctx.strokeStyle=lineColor;
		         ctx.stroke();
		         clearInterval(timer);
		         return false;
			}
	   	   else{
		   	  	var percent=(timeval-countdown)/timeval;
		   	  	//ctx.beginPath();
		   	  	//判断是否在0
		   	  	if(percent>0.25){
		   	  		var per=percent*2+1.5-2
		   	  	}
		   	  	else{
		   	  		var per=1.5+2*percent
		   	  	}
		   	  	ctx.arc(width/2,height/2,circleRadius,1.5*Math.PI,per*Math.PI); 
		   	  	ctx.strokeStyle="#ed8202";  
		   	  	ctx.strokeStyle=lineColor;
		        ctx.stroke();
	   	   }            
        },1000);      	
     	
}