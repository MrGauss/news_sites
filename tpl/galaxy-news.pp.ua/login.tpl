<div id="alogin">
[nologin]
<a href="/" data-role="dialog:open" data-dialog="loginform"><span>����</span></a>

        <div id="loginform" title="�����������" data-role="dialog:window" data-dopts="1" data-width="240">
            <form method="post" action="/" name="loginform" if="floginform">
                <p><input class="input" type="text" name="login" /></p>
                <p><input class="input" type="password" name="pass" /></p>
                <!-- p title="������ ������ ����������"><keygen name="security" keytype="rsa"></p -->
                <div class="fbutton">
                    <button class="button" type="submit">����</button>
                </div>
            </form>
            <div class="center">
                <a href="/">���������</a> | <a href="/" data-role="dialog:close">���������</a>
            </div>
        </div>
[/nologin]
[login]
<a href="/index.php?mod=admin" target="_blank">����</a>
[/login]
</div>