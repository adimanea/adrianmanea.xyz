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
<?php
include ("../includes/navigation-en.php");
?>

<h1 class="title">My pictures</h1>
<style>
 .gallery {
     overflow: hidden;
 }

 .row {
     display: flex;
     flex-wrap: wrap;
     padding: 20px;
 }

 /* Create four equal columns that sit next to each other */
 .column {
     flex: 25%;
     max-width: 25%;
     padding: 10px;
 }

 .column img {
     margin-top: 10px;
     margin-bottom: 10px;
     vertical-align: middle;
     
 }

 /* Responsive layout - makes a two column-layout instead of four columns */
 @media screen and (max-width: 800px) {
     .column {
         flex: 50%;
         max-width: 50%;
     }
 }

 /* Responsive layout - makes the two columns stack on top of each other instead of next to each other */
 @media screen and (max-width: 600px) {
     .column {
         flex: 100%;
         max-width: 100%;
     }
 }
</style>

</head>
<body>
    <div class="gallery">
        <div class="row">
            <div class="column">
                <a target="_blank" href="../images/gallery/hi-res/la/la14h.jpg">
                    <img src="../images/gallery/lo-res/la/la14.jpg" alt="Rays" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la03h.jpg">
                    <img src="../images/gallery/lo-res/la/la03.jpg" alt="Oradea by night" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la04h.jpg">
                    <img src="../images/gallery/lo-res/la/la04.jpg" alt="Citadel" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la05h.jpg">
                    <img src="../images/gallery/lo-res/la/la03.jpg" alt="Ruins" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la08h.jpg"
                    <img src="../images/gallery/lo-res/la/la08.jpg" alt="Birds" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la12h.jpg">
                    <img src="../images/gallery/lo-res/la/la12.jpg" alt="White Bridge" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la18h.jpg">
                    <img src="../images/gallery/lo-res/la/la18.jpg" alt="Bigar" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la09h.jpg">
                    <img src="../images/gallery/lo-res/la/la09.jpg" alt="Budapest" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la10h.jpg">
                    <img src="../images/gallery/lo-res/la/la10.jpg" alt="Budapest Parliament" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/po/po03h.jpg">
                    <img class="imgvert" src="../images/gallery/lo-res/po/po03.jpg" alt="Chariot" style="width:100%">
                </a>
            </div>
            <div class="column">
                <a target="_blank" href="../images/gallery/hi-res/la/la02h.jpg">
                    <img src="../images/gallery/lo-res/la/la02.jpg" alt="BW Sunset" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la06h.jpg">
                    <img src="../images/gallery/lo-res/la/la06.jpg" alt="Delta" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la07h.jpg">
                    <img src="../images/gallery/lo-res/la/la07.jpg" alt="Seal" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la13h.jpg">
                    <img src="../images/gallery/lo-res/la/la13.jpg" alt="Nailed it" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la01h.jpg">
                    <img src="../images/gallery/lo-res/la/la01.jpg" alt="Purple sunset" style=" width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la15h.jpg">
                    <img src="../images/gallery/lo-res/la/la15.jpg" alt="Foggy crest" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la16h.jpg">
                    <img src="../images/gallery/lo-res/la/la16.jpg" alt="Red cabin" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la17h.jpg">
                    <img src="../images/gallery/lo-res/la/la17.jpg" alt="Pensive" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/po/po04h.jpg">
                    <img class="imgvert" src="../images/gallery/lo-res/po/po04.jpg" alt="Krakow street" style="width:100%">
                </a>
            </div>
            <div class="column">
                <a target="_blank" href="../images/gallery/hi-res/la/la34h.jpg">
                    <img src="../images/gallery/lo-res/la/la34.jpg" alt="Prague" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la19h.jpg">
                    <img src="../images/gallery/lo-res/la/la19.jpg" alt="Cazane" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la20h.jpg">
                    <img src="../images/gallery/lo-res/la/la20.jpg" alt="Huniazi" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la22h.jpg">
                    <img src="../images/gallery/lo-res/la/la22.jpg" alt="Bridge" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la23h.jpg">
                    <img src="../images/gallery/lo-res/la/la23.jpg" alt="Mourning wall" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la24h.jpg">
                    <img src="../images/gallery/lo-res/la/la24.jpg" alt="Malbork Jesus" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la25h.jpg">
                    <img src="../images/gallery/lo-res/la/la25.jpg" alt="Statue" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la11h.jpg">
                    <img src="../images/gallery/lo-res/la/la11.jpg" alt="Budapest sunset" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/po/po05h.jpg">
                    <img class="imgvert" src="../images/gallery/lo-res/po/po05.jpg" alt="Crooked House" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/po/po02h.jpg">
                    <img class="imgvert" src="../images/gallery/lo-res/po/po02.jpg" alt="Old church" style="width:100%">
                </a>
            </div>
            <div class="column">
                <a target="_blank" href="../images/gallery/hi-res/la/la27h.jpg">
                    <img src="../images/gallery/lo-res/la/la27.jpg" alt="Gdansk" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la28h.jpg">
                    <img src="../images/gallery/lo-res/la/la28.jpg" alt="Gdansk sepia" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la29h.jpg">
                    <img src="../images/gallery/lo-res/la/la29.jpg" alt="Enthusiastic in Gdańsk" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la30h.jpg">
                    <img src="../images/gallery/lo-res/la/la30.jpg" alt="Gdańsk happy" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la31h.jpg">
                    <img src="../images/gallery/lo-res/la/la31.jpg" alt="Gdynia" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la32h.jpg">
                    <img src="../images/gallery/lo-res/la/la32.jpg" alt="Ship" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/la/la33h.jpg">
                    <img src="../images/gallery/lo-res/la/la33.jpg" alt="Pier" style="width:100%">
                </a>
                <a target="_blank" href="../images/gallery/hi-res/po/po01h.jpg">
                    <img class="imgvert" src="../images/gallery/lo-res/po/po01.jpg" alt="Oradea sepia" style="width:100%">
                </a>
            </div>
        </div>
    </div>
</body>
</html>
