<div class="elemblock admpage editpost" id="posteditor" data-post_id="{post:id}" data-hash_key="{hash:key}">

    <input type="hidden" name="post:id" value="{post:id}" data-role="uploader:post_id" data-area="post" data-save="1" />

    <div class="admpage_nav">
        <ul class="reset">
            <li class="active anim" data-area="main">������� ����</li>
            <li class="anim" data-area="seometa">SEO</li>
            <li class="anim" data-area="votes">����������</li>
            <li class="anim" data-area="access">������ �������</li>
            <li class="anim" data-area="linked_data">��'����� ����</li>
        </ul>
        <div class="buttons">
            <button type="button" class="type2" data-role="save">��������</button>
            <button type="button" class="type1" data-role="delete">��������</button>
            <button type="button" class="type3" data-role="exit">���������</button>
        </div>
    </div>

    <div class="adm_page_part active" data-area="main">

        <div class="editor_line">
            <div class="frame w33p lf_left" data-role="checkbox"><input class="input checkbox"  data-save="1" data-value="{post:posted}" type="checkbox" id="ch_posted" name="post:posted"><label class="label" for="ch_posted">�����������</label></div>
            <div class="frame w33p lf_left" data-role="checkbox"><input class="input checkbox"  data-save="1" data-value="{post:fixed}" type="checkbox"  id="ch_fixed"  name="post:fixed" ><label class="label blue" for="ch_fixed">����������� ���������</label></div>
            <div class="frame w33p lf_right" data-role="checkbox"><input class="input checkbox" data-save="1" data-value="{post:static}" type="checkbox" id="ch_static" name="post:static"><label class="label blue" for="ch_static">��������� ����� ������</label></div>
            <div class="clear"></div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">��������:</label></div>
            <div class="frame">
                <select size="3" data-bigsize="8" class="input select" data-save="1" name="categ:id" data-value="{categ:id}">
                    {categ:list}
                </select>
            </div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">���������:</label><span class="labelinfo">�� 250 �������</span></div>
            <div class="frame"><input class="input" type="text" name="post:title" data-save="1" value="{post:title}"></div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">�������� ����� ��������:</label><span class="labelinfo">�� 1000 �������</span></div>
            <div class="frame">
                {@include=bbpanel}
                <textarea id="shortpost" rows="4" class="input withbb" type="text" data-save="1" name="post:short_post">{post:short_post}</textarea>
            </div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">������ ����� ��������:</label></div>
            <div class="frame">
                {@include=bbpanel}
                <textarea id="fullpost" rows="10" class="input withbb" type="text" data-save="1" name="post:full_post">{post:full_post}</textarea>
            </div>
        </div>


    </div>

    <div class="adm_page_part dnone" data-area="seometa">

        <div class="editor_line">
            <div class="frame"><label class="label">����� �������������:</label><span class="labelinfo">����� ��������, �� 64 �������</span></div>
            <div class="frame">
                <input class="input" type="text" name="post:alt_title" data-save="1" value="{post:alt_title}">
            </div>

        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">�����:</label><span class="labelinfo">&lt;meta name=&quot;author&quot; content=&quot;...</span></div>
            <div class="frame"><input class="input" type="text" name="post:author" data-save="1" value="{post:author}"></div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">���� ���������:</label><span class="labelinfo">&lt;meta name=&quot;description&quot; content=&quot;...</span></div>
            <div class="frame"><textarea rows="2" class="input textarea" type="text" data-save="1" name="post:descr">{post:descr}</textarea></div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">������ �����:</label><span class="labelinfo">&lt;meta name=&quot;keywords&quot; content=&quot;...</span></div>
            <div class="frame"><input class="input" type="text" name="post:keywords" data-save="1" value="{post:keywords}"></div>
        </div>

        <div class="editor_line">
            <div class="frame"><label class="label">������� ��������:</label><span class="labelinfo">&lt;link rel=&quot;image_src&quot; href=&quot;...</span></div>
            <div class="frame"><input class="input" type="text" name="post:keywords" data-save="1" value="{post:main_img}"></div>
        </div>
    </div>
    <div class="adm_page_part dnone" data-area="votes">
        <div><input class="input checkbox" type="checkbox" id="ch111"><label class="label" for="ch111">testing 1</label></div>
        <div><input class="input checkbox" type="checkbox" id="ch112"><label class="label red" for="ch112">testing 2</label></div>
        <div><input class="input checkbox" type="checkbox" id="ch113"><label class="label blue" for="ch113">testing 3</label></div>

    </div>
    <div class="adm_page_part dnone" data-area="access">3</div>
    <div class="adm_page_part dnone" data-area="linked_data">4</div>

    <div class="clear"></div>
</div>