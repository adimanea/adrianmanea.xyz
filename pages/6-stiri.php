<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <?php
    include ("../includes/head.php");
    ?>
</head>
<header>
    <?php
    include ("../includes/header.php");
    ?>
</header>
<body>
    <?php
    include ("../includes/navigation.php");
    ?>
    
    <h1 class="title">Știri</h1>
    <div class="articol">
    <p> 
        Mai jos sînt știrile pe care le urmăresc, colectate automat din feed-uri RSS cu <a href="https://git.codemadness.org/sfeed/file/README.html"><b>sfeed</b></a> și actualizate o dată la 3 ore.
    </p>
    <?php
        include('feeds.html');
    ?>
</div>
</body>
</html>
