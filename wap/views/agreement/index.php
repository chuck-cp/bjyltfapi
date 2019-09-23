<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <title><?=$title?></title>
        <style>
            /*标签初始化*/
            * {margin: 0;padding: 0 }
            body{ background: #fff; -webkit-overflow-scrolling: touch; color: #333; font-size: 13px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
            em{ font-style: normal;}
            p{ margin: 0; padding: 0;}
            .protocol{
                padding: 10px 3.75% 0;
                color: #333;
                overflow: hidden;
                background-color: #fff;
            }
            .protocol h1{
                color: #000;
                font-size: 16px;
                font-weight: normal;
                padding: 3px 0;
            }
            .protocol p{
                padding: 5px 0;
                font-size: 14px;
                line-height: 22px;

            }
        </style>
    </head>
    <body>
        <?=$content?>
    </body>
</html>