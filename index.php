<!doctype html>
<meta charset="utf-8">
<head>
    <?php
    include ("includes/head.php");
    ?>
</head>
<header>
    <?php
    include ("includes/header.php");
    ?>
</header>
<body>
    <?php
    include ("includes/navigation.php");
    ?>
    <?php
    $sDirectory     = 'pages-random';
    if( is_dir( $sDirectory ) ) 
    {
        $rDir = opendir( $sDirectory );
        while( ( $sFile = readdir( $rDir ) ) !== FALSE ) 
        {
            if( ( $sFile == '.' ) || ( $sFile === '..' ) )
            {
                continue;
            }
            $aFiles[] = $sDirectory . '/' . $sFile;
        }
    }
    srand(mktime(0, 0, 0));
    $sRandom = array_rand( $aFiles );
    require_once( $aFiles[ $sRandom ] );
    ?>
</body>
</html>
