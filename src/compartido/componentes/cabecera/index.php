<?php
    function cabecera($titulo){
        echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
                <link rel="stylesheet" href="./cabecera.css">
            </head>
            <body>
                <header class="header">
                    <div class="header__backbutton">
                        <a href="../../index.php" type="button">
                            <svg width="32px" height="32px" viewBox="0 0 24 24" stroke-width="1.9" fill="none" xmlns="http://www.w3.org/2000/svg" color="#FFF">
                                <path d="M21 12L3 12M3 12L11.5 3.5M3 12L11.5 20.5" stroke="#FFF" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                    <h2 class="header__title">
                        '.$titulo.'
                    </h2>
                    <div class="header__logo">
                        <img src="../../static/logo.png" alt="Logo">
                    </div>
                </header>
            </body>
            </html>
        ';
    }
?>