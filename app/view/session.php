<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request Logger - View Session</title>
    <meta charset="utf-8">
    <meta name="copyright" content="This was created by https://twitter.com/adamtlangley" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/style.css">
 </head>
<body>
<h1>Request Logger</h1>
<h3>Listening for requests for the below domain</h3>
<h3><?php echo $data["hash"]; ?>.<?php echo \Controller\Domain::get(); ?></h3>
<div class="holder">
    <div class="requests"></div>
    <div class="content"></div>
</div>
<script src="/script.js"></script>
<script>
    new RequestCatcher('<?php echo $data["hash"]; ?>',<?php echo json_encode($data["data"]); ?>);
</script>
</body>
</html>