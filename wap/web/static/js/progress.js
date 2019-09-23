let Progress = function(init){
    this.init(init)
};
Progress.prototype= {
    init:function (init) {
    	var timestamp = Date.parse(new Date())/1000+10;
        console.log(timestamp);
        this.el = init.el;//元素ID
        let cvsElement = document.getElementById(this.el),//获取元素
        ctx=cvsElement.getContext("2d"),//获取画笔
        width = cvsElement.width,//元素宽度
        height=cvsElement.height,//元素高度
        degActive=0,//动态线条
        timer=null;//定时器
        
        //停止时的角度
        init.deg>0&&init.deg<=100?
            this.deg = init.deg:this.deg = 100;
        
        //线宽
        init.lineWidth !== undefined?
            this.lineWidth = init.lineWidth : this.lineWidth =20;

        //判断宽高较小者
        this.wh = width>height?height:width;

        //设置圆的半径，默认为宽高较小者
        init.circleRadius>0&&init.circleRadius<=this.wh/2-this.lineWidth/2?
            this.circleRadius = init.circleRadius:this.circleRadius = this.wh/2-this.lineWidth/2;

        //绘制线的背景颜色
        init.lineBgColor !==undefined?
            this.lineBgColor = init.lineBgColor:this.lineBgColor='#ccc';

        //绘制线的颜色
        init.lineColor !==undefined?
            this.lineColor = init.lineColor:this.lineColor='#009ee5';

        //绘制文字颜色
        init.textColor !==undefined?
            this.textColor = init.textColor:this.textColor='#009ee5';

        //绘制文字大小
        init.fontSize !==undefined?
            this.fontSize = init.fontSize:this.fontSize=parseInt(this.circleRadius/2);

        //执行时间
        this.timer = init.timer;
        console.log('opentime'+init.opentime)
        //清除锯齿
        if (window.devicePixelRatio) {
            cvsElement.style.width = width + "px";
            cvsElement.style.height = height + "px";
            cvsElement.height = height * window.devicePixelRatio;
            cvsElement.width = width * window.devicePixelRatio;
            ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        }
        //设置线宽
        ctx.lineWidth=this.lineWidth;
        //获取打开页面的时间
        this.opentime=init.opentime;
        //倒计时是加你
        this.timeval=init.timeval;
        //启动定时器
        timer = setInterval(function(){
        	//实时获取当前时间
        	var timestamp = Date.parse(new Date())/1000;
        	//获取时间差
        	var countdown=this.opentime-timestamp;        	
            ctx.clearRect(0,0,width,height);//清除画布
            ctx.beginPath();//开始绘制底圆
            ctx.arc(width/2,height/2,this.circleRadius,1,8);
            ctx.strokeStyle=this.lineBgColor;
            ctx.stroke();  
            ctx.beginPath();//开始绘制动态圆  
			if (countdown <= 0) {
		   	  	 ctx.arc(width/2,height/2,this.circleRadius,0,360);
		   	  	 ctx.strokeStyle="green"; 
		   	  	 ctx.strokeStyle=this.lineColor;
		         ctx.stroke();
		         clearInterval(timer);
		         return false;
			}
	   	   else{
		   	  	var percent=(this.timeval-countdown)/this.timeval;
		   	  	//ctx.beginPath();
		   	  	//判断是否在0
		   	  	if(percent>0.25){
		   	  		var per=percent*2+1.5-2
		   	  	}
		   	  	else{
		   	  		var per=1.5+2*percent
		   	  	}
		   	  	ctx.arc(width/2,height/2,this.circleRadius,1.5*Math.PI,per*Math.PI); 
		   	  	ctx.strokeStyle="#ed8202";  
		   	  	ctx.strokeStyle=this.lineColor;
		        ctx.stroke();
	   	   }            
        }.bind(this),1000);
    }
 };
