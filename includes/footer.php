<?php $s = settings_all(); ?>
</main>
<footer class="site-footer">
  <div class="container footer__inner">
    <div>
      <div class="brand brand--footer"><span class="brand__icon">&#9992;</span><span class="brand__name"><?= h($s['company_name']) ?></span></div>
      <p class="footer__tag">24/7 bereikbaar · Vaste, transparante tarieven</p>
    </div>
    <div class="footer__col">
      <h4>Contact</h4>
      <p><a href="<?= h(tel_href($s['company_phone'])) ?>"><?= h($s['company_phone']) ?></a><br>
         <a href="mailto:<?= h($s['company_email']) ?>"><?= h($s['company_email']) ?></a></p>
    </div>
    <div class="footer__col">
      <h4>Info</h4>
      <p>
        <a href="<?= h(url('?p=privacyverklaring')) ?>">Privacy</a> ·
        <a href="<?= h(url('?p=cookies')) ?>">Cookies</a> ·
        
      </p>
    </div>
  </div>
  <div class="footer__bottom">&copy; <?= date('Y') ?> <?= h($s['company_name'] ) ?> - Design By Webdesignx.nl</div>
</footer>
</body>
</html>
