<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title><?php echo $title;?></title>
</head>
<style>
    *{ padding: 0; margin: 0}
    body{ font-size: 14px; color: #333}
    ul li{ list-style: none}
    .content{padding:10px;}
    .content p{ line-height: 23px; text-indent: 2em}
    .content img{ display: block; width: 100%}
</style>
<body>
    <div class="content">
        <?php echo $content;?>
    </div>
</body>
</html>