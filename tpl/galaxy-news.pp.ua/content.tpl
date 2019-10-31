<!DOCTYPE HTML>
<html lang="ua">
    <!-- Використано RAM:   {stats:used_memory} -->
    <!-- Запитів до БД:     {stats:queries} -->
    <!--    поміщено в кеш: {stats:cached} -->
    <!-- КОРИСТУВАЧІВ:      {stats:user_count} -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="{title}">
        <meta name="application-name" content="MrGauss CMS">

        <meta property="og:image"     content="{og:image}" />
        <meta property="og:title"     content="{title}" />
        <meta property="og:url"       content="{og:url}" />
        <meta property="og:site_name" content="{og:site_name}" />

        <base href="{HOME}">
        <title>{title}</title>                

        <link rel="image_src" href="{og:image}" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.structure.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.theme.css" media="screen" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="{SKINDIR}/js/main.lib.js" type="text/javascript"></script>

        <link href="https://fonts.googleapis.com/css?family=Comfortaa|Ubuntu&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/style.css?rand={RAND}" media="screen" />
    </head>
    <body>

        <div id="page">
            <div id="leftside">
                <div class="header">
                    <a id="logo" href="{HOME}">CKnews</a>
                    <a id="sublogo"  href="{HOME}">новини Черкащини</a>
                </div>
                <div class="container">
                    <ul>
                        {taglist:tag_element}
                    </ul>
                    <div class="clr"></div>
                </div>
                <div class="clr"></div>
                <div class="bottom">
                    &nbsp;
                </div>
            </div>
            <div id="mainbox">
                <div class="header">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li><a href="/tag:%C2%B3%E9%ED%E0+%ED%E0+%F1%F5%EE%E4%B3/" title="&Acy;&Tcy;&Ocy;-&Ocy;&Ocy;&Scy;">Війна</a></li>
                        <li><a href="/tag:%D0%9A%D1%83%D0%BB%D1%8C%D1%82%D1%83%D1%80%D0%B0/" title="&Kcy;&ucy;&lcy;&softcy;&tcy;&ucy;&rcy;&acy;">Культура</a></li>
                        <li><a href="/tag:%D0%9D%D0%BE%D0%B2%D0%B8%D0%BD%D0%B8/" title="&Ncy;&ocy;&vcy;&icy;&ncy;&icy;">Новини</a></li>
                        <li><a href="/tag:%D0%9F%D0%BE%D0%B3%D0%BE%D0%B4%D0%B0/" title="&Pcy;&ocy;&gcy;&ocy;&dcy;&acy;">Погода</a></li>
                        <li><a href="/tag:%D0%9F%D0%BE%D0%BB%D1%96%D1%82%D0%B8%D0%BA%D0%B0/" title="&Pcy;&ocy;&lcy;&iukcy;&tcy;&icy;&kcy;&acy;">Політика</a></li>
                        {global:login}
                    </ul>
                    <div class="clr"></div>
                </div>
                [notarea:fullpost]
                <div class="articles">
                    <h2>Новини Черкащини</h2>
                    {global:posts}
                </div>
                <div class="right_top">
                    <h2>Райони</h2>             <div class="right_side_img">{custom:0-2:tags:25:topnews_bottom}</div><ul>{custom:2-2:tags:25:topnews}</ul>
                    <h2>Економіка</h2>          <div class="right_side_img">{custom:0-1:tags:35:topnews_bottom}</div><ul>{custom:1-2:tags:35:topnews}</ul>
                    <h2>Політика</h2>           <div class="right_side_img">{custom:0-2:tags:27,23:topnews_bottom}</div><ul>{custom:2-2:tags:27,23:topnews}</ul>
                    <h2>Освіта та культура</h2> <div class="right_side_img">{custom:0-1:tags:33,34:topnews_bottom}</div><ul>{custom:1-2:tags:33,34:topnews}</ul>
                </div>
                [/notarea]

                [area:fullpost]
                    {global:posts}
                [/area]

                <div class="clr"></div>

                <div id="footer">
                    <div class="shortimg">
                        {custom:10-12:tags:0:topnews_bottom}
                        <div class="clr"></div>
                    </div>
                    <div class="bottom">З питань функціонування сайту звертайтесь за адресою admin@{DOMAIN}</div>
                </div>

                <div class="clr"></div>
            </div>
            <div class="clr"></div>
        </div>
    </body>
</html>