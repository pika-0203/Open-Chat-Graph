<main>
    <hr>
    <article>
        <?php foreach (session()->getError() as $error) : ?>
            <p><?php h($error['message']) ?></p>
        <?php endforeach ?>
        <?php if (session()->has('message')) : ?>
            <?php h(session('message')) ?>
        <?php endif ?>
        <section>
            <aside>
                <a href="/auth/login?return_to=test">LINEでログイン</a>
            </aside>
            <aside>
                <a href="/auth/logout?return_to=test">ログアウト</a>
            </aside>
        </section>
        <section>
            <form action="/openchat/add" method="POST">
                <input type="text" name="url">
                <button type="submit">追加</button>
            </form>
            <?php echo __DIR__ ?>
        </section>
    </article>
</main>