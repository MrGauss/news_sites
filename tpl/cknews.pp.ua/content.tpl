<!DOCTYPE HTML>
<html lang="ua">
    <!-- Використано RAM:   {stats:used_memory} -->
    <!-- Запитів до БД:     {stats:queries} -->
    <!--    поміщено в кеш: {stats:cached} -->
    <!-- КОРИСТУВАЧІВ:      {stats:user_count} -->
    <!-- {stats:user_count_periodic}
    -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="{title}">
        <meta name="keywords" content="Новини, ЧЕркаси">
        <meta name="application-name" content="MrGauss CMS">
        <meta name="google-site-verification" content="RE5sWBnQ2kuLU7jopEtZ7E-Whr_GyEePDfOfIbi-gZg" />
        <meta name="telegram:channel" content="@cknewsppua">

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
        <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

        <script src="{SKINDIR}/js/main.lib.js?ver=5.12" type="text/javascript"></script>

        <link href="https://fonts.googleapis.com/css?family=Comfortaa|Ubuntu&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/style.css?ver=5.12" media="screen" />
    </head>
    <body>

        <div id="page">
            <div id="leftside">
                <div class="header">
                    <a id="inflogo" href="{HOME}">ІНФОРМАЦІЙНИЙ РЕСУРС</a>
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
                    {global:login}
                </div>
            </div>
            <div id="mainbox">
                <div class="header">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li><a href="/tag:%C2%B3%E9%ED%E0+%ED%E0+%F1%F5%EE%E4%B3/" title="&Vcy;&iukcy;&jcy;&ncy;&acy; &ncy;&acy; &scy;&khcy;&ocy;&dcy;&iukcy;">Війна на сході</a></li>
                        <li><a href="/tag:%C5%EA%EE%ED%EE%EC%B3%EA%E0/" title="&IEcy;&kcy;&ocy;&ncy;&ocy;&mcy;&iukcy;&kcy;&acy;">Економіка</a></li>
                        <li><a href="/tag:%CA%F3%EB%FC%F2%F3%F0%E0/" title="&Kcy;&ucy;&lcy;&softcy;&tcy;&ucy;&rcy;&acy;">Культура</a></li>
                        <li><a href="/tag:%CC%E5%F0%B3%FF/" title="&Mcy;&iecy;&rcy;&iukcy;&yacy;">Мерія</a></li>
                        <li><a rel="nofollow noreferrer" target="_blank" title="Наш канал в TELEGRAM" href="https://t.me/cknewsppua">TELEGRAM</a></li>
                        <li><a href="/rss.xml" target="_blank">RSS</a></li>
                    </ul>
                    <div class="clr"></div>
                </div>
                [notarea:fullpost]
                <div class="articles">
                    <h2>Новини Черкащини</h2>
                    {global:posts}
                </div>
                <div class="right_top">
                    <h2>Райони</h2>             <div class="right_side_img">{custom:0-2:tags:6:topnews_bottom}</div><ul>{custom:2-2:tags:6:topnews}</ul>
                    <h2>Економіка</h2>          <div class="right_side_img">{custom:0-1:tags:15:topnews_bottom}</div><ul>{custom:1-2:tags:15:topnews}</ul>
                    <h2>Політика</h2>           <div class="right_side_img">{custom:0-2:tags:11:topnews_bottom}</div><ul>{custom:2-2:tags:11:topnews}</ul>
                    <h2>Освіта та культура</h2> <div class="right_side_img">{custom:0-1:tags:3,13:topnews_bottom}</div><ul>{custom:1-2:tags:3,13:topnews}</ul>
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