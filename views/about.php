<html>
    <head>
        <title>Mikroengine Framework</title>
        <style>
            *{
                font-family: Verdana;
            }
            body{
                font-size: 12px;
            }
            h2{
                font-size: 18px;
            }
        </style>
    </head>
    <body>
        <h2>Welcome</h2>
        <?php echo $content; ?>
        <div>Memory usage: {memory_usage} • Memory peak: {memory_peak_usage} • Load time: {total_time}</div>
    </body>
</html>