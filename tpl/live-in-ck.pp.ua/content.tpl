<!DOCTYPE HTML>
<html lang="ua">
    <!-- Використано RAM:   {stats:used_memory} -->
    <!-- Запитів до БД:     {stats:queries} -->
    <!--    поміщено в кеш: {stats:cached} -->
    <!-- КОРИСТУВАЧІВ:      {stats:user_count} -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={charset}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="google-site-verification" content="yfffl6SCPYczNYIjU-gfJoJgR85KNXizTSSsa6w2RZg" />
        <meta name="description" content="Test site">
        <meta name="keywords" content="CMS">
        <meta name="author" content="MrGauss">
        <meta name="application-name" content="Gauss CMS">

        <base href="{HOME}">
        <title>{title}</title>

        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/style.css" media="screen" />         
    </head>
    <body>

        <div id="page">
            <nav class="wpage topnav noselect">
                <ul class="reset nav">
                    <li><a href="/"><span>Головна сторінка</span></a></li>
                    <li><a href="/"><span>Правила сайту</span></a></li>
                    <li><a href="/"><span>Зворотній зв'язок</span></a></li>
                    {global:login}
                </ul>
                <div class="clear"></div>
            </nav>
            <div class="clear"></div>

            <div class="wpage logo noselect">
                <a href="/"><b><u>cmska</u>.org</b><i>Omnia mutantur, nihil interit</i></a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>

            <nav class="wpage logonav">
                <ul class="reset logonav">
                    <li><a href=""><span>Про систему</span></a></li>
                    <li><a href=""><span>Завантажити</span></a></li>
                    <li class="active"><a href=""><span>Блог розробників</span></a></li>
                    <li><a href=""><span>Релізи</span></a></li>
                    <li><a href=""><span>Доповнення</span></a>
                        <div class="submenu">
                            <ul class="reset subnav">
                                <li><a href="/">Дрібні хаки</a></li>
                                <li><a href="/">Зовнішній вигляд</a></li>
                                <li><a href="/">Нові функції</a></li>
                            </ul>
                        </div>
                    </li>


                    <li><a href=""><span>Форум</span></a></li>
                </ul>
                <div class="clear"></div>
            </nav>
            <div class="clear"></div>

            <div class="wpage content">
                <main id="content" data-area="{AREA}">
                    {global:info}
                    {global:posts}
                </main>

                [notarea:fullpost]
                <aside id="aside">
                    <div class="frame">
                        <div id="stats_cms">
                            <p class="ram"><span>Використано RAM:</span><b>{stats:used_memory}</b></p>
                            <p class="db"><span>Запитів до БД:</span><b>{stats:queries}</b></p>
                            <p class="db sub"><span>SELECT:</span><b>{stats:select}</b></p>
                            <p class="db subsub"><span>поміщено в кеш:</span><b>{stats:cached}</b></p>
                            <p class="db sub"><span>INSERT:</span><b>{stats:insert}</b></p>
                            <p class="db sub"><span>UPDATE:</span><b>{stats:update}</b></p>
                            <p class="db sub"><span>DELETE:</span><b>{stats:delete}</b></p>
                            <div class="clear"></div>
                        </div>
                    </div>
                </aside>
                [/notarea]

                <div class="clear"></div>
            </div>
            <div class="clear"></div>

            <div class="wpage footer">
                cmska.org &copy; 2007-2019<div class="clear"></div>
            </div>
            <div class="clear"></div>

        </div>

        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.structure.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/jquery-ui.theme.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{SKINDIR}/css/bbcode.css" media="screen" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="{SKINDIR}/js/main.lib.js" type="text/javascript"></script>

    </body>
</html>