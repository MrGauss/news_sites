<!DOCTYPE HTML>
<html lang="ua">
    <!-- Використано RAM:   {stats:used_memory} -->
    <!-- Запитів до БД:     {stats:queries} -->
    <!--    поміщено в кеш: {stats:cached} -->
    <!-- КОРИСТУВАЧІВ:      {stats:user_count} -->
    <!-- _AREA_:            {AREA} -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="{title}">
        <meta name="application-name" content="MrGauss CMS">
        <meta name="google-site-verification" content="cSNYgZVO3GZ67Qq_3kovk3y5W6Qzp9uIkx58FUC0alM" />
        <meta name="telegram:channel" content="@it_news_pp_ua">

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
        <link rel="sitemap"    type="application/xml" title="Sitemap" href="/sitemap.xml" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="{SKINDIR}/js/main.lib.js" type="text/javascript"></script>

        <link href="https://fonts.googleapis.com/css?family=Comfortaa|Ubuntu&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/style.css?ver=1" media="screen" />
    </head>
    <body>

        <div id="page">

            <div id="leftside">
                <div class="header">
                    <a id="inflogo" href="{HOME}">ІНФОРМАЦІЙНИЙ РЕСУРС</a>
                    <a id="logo" href="{HOME}">IT-NEWS</a>
                    <a id="sublogo" href="{HOME}">наука та технології</a>
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
                        <li><a href="/tag:Hardware/" title="Hardware">Hardware</a></li>
                        <li><a href="/tag:Software/" title="Software">Software</a></li>
                        <li><a href="/tag:%C2%B3%F0%F3%F1%E8/" title="&Vcy;&iukcy;&rcy;&ucy;&scy;&icy;">Віруси</a></li>
                        <li><a href="/tag:%CD%E0%F3%EA%E0/" title="&Ncy;&acy;&ucy;&kcy;&acy;">Наука</a></li>
                        <li><a href="/rss.xml" target="_blank">RSS</a></li>
                        <li><a rel="nofollow noreferrer" target="_blank" title="Наш канал в TELEGRAM" href="https://t.me/it_news_pp_ua">TELEGRAM</a></li>
                        <li><a href="/post/1-pro_nas.html">Про нас</a></li>
                    </ul>
                    <div class="clr"></div>
                </div>
                [notarea:fullpost]
                <div class="articles">
                    [area:main]
                        [nopages]
                            {custom:0-1:tags:25,26,27,28,29,30,31,32,33,34,35:short_post_top}
                        [/nopages]
                    [/area:main]
                    <h2>Останні новини</h2>
                    {global:posts}
                </div>
                <div class="right_top">
                    <h2>Кібербезпека</h2><div class="right_side_img">{custom:2-2:tags:11:topnews_bottom}</div>      <ul>{custom:2-2:tags:11:topnews}</ul>
                    <h2>Технології</h2>  <div class="right_side_img">{custom:0-1:tags:8:topnews_bottom}</div>       <ul>{custom:2-3:tags:8:topnews}</ul>
                    <h2>Хакери</h2>      <div class="right_side_img">{custom:0-1:tags:5,13:topnews_bottom}</div>    <ul>{custom:1-4:tags:5,13:topnews}</ul>
                    <h2>Наука</h2>       <ul>{custom:0-10:tags:6,7:topnews}</ul>
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