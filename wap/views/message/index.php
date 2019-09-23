<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?=$title?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <style>
        /*标签初始化*/
        * {margin: 0;padding: 0 }
        table {border-collapse: collapse;border-spacing: 0}
        h1,h2,h3,h4,h5,h6 {font-size: 100%}
        ul,ol,li {list-style: none}
        em,i {font-style: normal}
        img {border: 0;display:inline-block}
        input,img {vertical-align: middle; border:none; }
        a {color: #333;text-decoration: none;-webkit-tap-highlight-color:transparent;}
        input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;  }
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body{ -webkit-overflow-scrolling: touch;}
        p{ margin: 0; padding: 0;}
        .wrapper{ padding-top: 10px;}
        .time{ color: #9c9c9c; font-size: 13px; padding:10px 4.25%; text-align: center;}
        .title{padding: 0 10px 5px 10px; text-align: center; color: #f08e67; font-weight:normal; line-height: 24px;}
        .content{ padding: 10px; margin: 0 4.25%; border: 1px solid #ee956d; border-radius: 5px; margin-top: 5px;}
        .content p{ line-height: 25px; font-size: 14px; text-indent: 2em; color: #f08e67;}
        .wrapper img{width: 100%;height: auto;display: block;}
    </style>
</head>
<body>
<div class="wrapper">
    <p class="time"><?=$date?></p>
    <h4 class="title"><?=$bt?></h4>
    <div class="content">
        <?=$content?>
    </div>
</div>
</body>
</html>
