<section class="article short">
    <header class="article_top">
        <div class="topcateg date"><span>{post:created_time:Y.m.d}</span></div>
        <div class="topcateg anim"><a rel="chapter" title="{categ:name}" href="{categ:url}">{categ:name}</a></div>
        <h1>{post:title}</h1>
    </header>

    <div class="post_short_image">
        <img src="[full-image:0]{full-image:0:force-mini}[/full-image][full-noimage:0]{SKINDIR}/img/spacer.gif[/full-noimage]" alt="{post:title}" title="{post:title}">
    </div>
    <div class="text">{post:short_post:strip}<div class="clear"></div></div>
    <div class="clear"></div>

    <div class="attr_panel">
        <a class="more" href="{post:url}" title="{post:title} | {post:keywords}" rel="details">Детальніше...</a>
        <div class="taglist">
            {taglist}
        </div>
    </div>
</section>