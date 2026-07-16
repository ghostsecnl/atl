<?php
declare(strict_types=1);
$s = settings_all();
$pdo = db();
$airports = $pdo->query('SELECT id,name FROM airports ORDER BY sort_order')->fetchAll();
$regions  = $pdo->query('SELECT id,name FROM regions ORDER BY sort_order')->fetchAll();
$page_title = $s['company_name'] . ' | Voordelig & betrouwbaar naar de luchthaven';
$page_desc  = 'Boek voordelig en betrouwbaar airport taxivervoer vanuit heel Limburg naar Schiphol, Eindhoven, Dusseldorf, Brussel en meer. Vaste tarieven, 24/7 beschikbaar.';
?>
<section class="hero">
  <div class="container hero__inner">
    <div class="hero__left">
      <span class="pill-badge"><span class="dot"></span> Vaste prijzen · Altijd op tijd</span>
      <h1 class="hero__title">Voordelig &amp; betrouwbaar naar de <span class="gold-text">luchthaven</span></h1>
      <p class="hero__lead">Comfortabel deur-tot-deur luchthavenvervoer vanuit heel Zuid- en Midden-Limburg. Vaste, transparante tarieven, professionele chauffeurs en vluchtmonitoring.</p>
      <div class="hero__ctas">
        <a href="#boeken" class="btn btn-primary btn-lg">Bereken &amp; boek je rit &rarr;</a>
        <a href="<?= h(tel_href($s['company_phone'])) ?>" class="btn btn-ghost btn-lg">Bel direct</a>
      </div>
      <ul class="usp-strip">
        <li>✓ 24/7 beschikbaar</li>
        <li>✓ Vluchtmonitoring</li>
        <li>✓ Vaste tarieven</li>
      </ul>
    </div>

    <div id="boeken" class="hero__right">
      <div class="booking-card">
        <h2>Bereken uw ritprijs</h2>
        <form id="quoteForm" class="booking-form" >
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
          <div class="tabs">
            <label><input type="radio" name="direction" value="to_airport" checked> Naar luchthaven</label>
            <label><input type="radio" name="direction" value="to_home"> Vanaf luchthaven</label>
          </div>

          <label id="regionField">
            <span id="label-text-regio">Regio</span>
            <select name="region_id" id="select-regio" required>
               <?php foreach ($regions as $r): ?><option value="<?= h($r['id']) ?>"><?= h($r['name']) ?></option><?php endforeach; ?>
            </select>
          </label>

          <label id="airportField">
            <span id="label-text-airport">Luchthaven</span>
            <select name="airport_id" id="select-luchthaven" required>
               <?php foreach ($airports as $a): ?><option value="<?= h($a['id']) ?>"><?= h($a['name']) ?></option><?php endforeach; ?>
            </select>
          </label>

          <div class="grid-3">
            <label>Personen
              <select name="passengers">
                <?php for ($i=1;$i<=8;$i++) echo "<option value=\"$i\"".($i===2?' selected':'').">$i</option>"; ?>
              </select>
            </label>
            <label>Bagage groot
              <select name="luggage">
                <?php for ($i=0;$i<=4;$i++) echo "<option value=\"$i\"".($i===2?' selected':'').">$i</option>"; ?>
              </select>
            </label>
            <label>Bagage klein
              <select name="luggage1">
                <?php for ($i=0;$i<=4;$i++) echo "<option value=\"$i\"".($i===2?' selected':'').">$i</option>"; ?>
              </select>
            </label>
          </div>

          <div class="tabs">
            <label><input type="radio" name="trip_type" value="oneway" checked> Enkele reis</label>
            <label><input type="radio" name="trip_type" value="return" id="returnRadio"> Retour</label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg btn-block">Bereken prijs</button>
          <div id="quoteResult" class="quote-result" hidden></div>
        </form>

        <form id="bookForm" class="booking-form" style="display: none;">
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="quote_json" id="quoteJson">
          <h3>Uw gegevens</h3>
          <div class="grid-2">
            <label>Postcode:<input type="text" id="postcode" name="postcode" required></label>
            <label>Huisnummer:<input type="text" id="huisnummer" name="huisnummer" required></label>
          </div>
          <div class="grid-2">
            <label>Plaats:<input type="text" id="plaats" name="plaats"></label>
            <label>Straatnaam:<input type="text" id="straat" name="straat"></label>
          </div>
          <div class="grid-2">
            <label>Naam<input type="text" name="customer_name" required></label>
            <label>Telefoon<input type="tel" name="customer_phone" required></label>
          </div>
          <label>E-mail<input type="email" name="customer_email" required></label>
          <div class="grid-2">
            <label>Ophaal datum<input type="date" name="pickup_date" required></label>
            <label>Ophaal tijd<input type="time" name="pickup_time" required></label>
          </div>
          <div id="returnFields" class="grid-2" style="display:none;">
            <label>Retour datum<input type="date" name="return_date"></label>
            <label>Retour tijd<input type="time" name="return_time"></label>
          </div>

          <div class="grid-2">
            <label>Vluchtnummer:<input type="text" name="flight_number" placeholder="KL1234" required></label>
            <div>
              <label>Betaalmethode</label>
              <div class="tabs">
                <label><input type="radio" name="payment_method" value="cash" checked> Contant</label>
                <label><input type="radio" name="payment_method" value="pin"> Pinnen</label>
              </div>
            </div>
          </div>
          <label>Opmerkingen<textarea name="notes" rows="3"></textarea></label>
          
          <button type="submit" class="btn btn-gold btn-lg btn-block" id="bookBtn">Boeking bevestigen</button>
          <button type="button" class="btn btn-ghost-ink btn-sm" id="editQuote">← Prijs opnieuw berekenen</button>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="gold-bar">
  <div class="container gold-bar__inner">
    <strong>24/7 telefonisch reserveren:</strong>
    <a class="gold-bar__phone" href="<?= h(tel_href($s['company_phone'])) ?>"><?= h($s['company_phone']) ?></a>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head section-head--center">
      <span class="eyebrow">Waarom Airport Taxi Limburg</span>
      <h2>Zorgeloos op weg naar elke luchthaven</h2>
      <p>Al jarenlang het vertrouwde adres voor luchthavenvervoer in Limburg. Scherpe vaste tarieven, persoonlijke service en betrouwbaarheid.</p>
    </div>
    <div class="grid-3 features">
      <div class="feature"><div class="feature__icon">€</div><h3>Vaste tarieven</h3><p>Transparante, voordelige prijzen zonder verrassingen.</p></div>
      <div class="feature"><div class="feature__icon">⏱</div><h3>24/7 beschikbaar</h3><p>Dag en nacht, ook op feestdagen, altijd op tijd.</p></div>
      <div class="feature"><div class="feature__icon">✈</div><h3>Vluchtmonitoring</h3><p>Wij volgen uw vlucht en passen de ophaaltijd aan.</p></div>
      <div class="feature"><div class="feature__icon">🚐</div><h3>Deur tot deur</h3><p>Comfortabel opgehaald en afgezet, taxibusjes tot 8 personen.</p></div>
      <div class="feature"><div class="feature__icon">★</div><h3>Ervaren chauffeurs</h3><p>Nederlandstalige, professionele chauffeurs.</p></div>
      <div class="feature"><div class="feature__icon">💳</div><h3>Contant of pin</h3><p>Betaal makkelijk in de taxi zelf.</p></div>
    </div>
  </div>
</section>

<section class="section section--slate">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Onze bestemmingen</span>
      <h2>Taxi naar elke luchthaven in de regio</h2>
    </div>
    <div class="link-grid">
      <?php foreach ($airports as $a): ?>
        <a class="link-chip" href="<?= h(url('?luchthaven=' . $a['id'])) ?>">✈ <span><?= h($a['name']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--navy">
  <div class="container">
    <div class="section-head section-head--center">
      <span class="eyebrow">Zo werkt het</span>
      <h2 style="color:#fff;">In drie stappen geboekt</h2>
    </div>
    <div class="steps">
      <div class="step"><div class="step__num">1</div><h3>Bereken je ritprijs</h3><p>Vul postcode, huisnummer en luchthaven in.</p></div>
      <div class="step"><div class="step__num">2</div><h3>Reisgegevens</h3><p>Kies datum, tijd, vluchtnummer en betaalmethode.</p></div>
      <div class="step"><div class="step__num">3</div><h3>Ontvang bevestiging</h3><p>Direct per e-mail. Wij zorgen voor de rest.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Ons werkgebied</span>
      <h2>Vanuit heel Zuid- en Midden-Limburg</h2>
    </div>
    <div class="link-grid">
      <?php foreach ($regions as $r): ?>
        <a class="link-chip" href="<?= h(url('?regio=' . $r['id'])) ?>">📍 <span><?= h($r['name']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
  window.__ATL = { 
    quoteUrl: '<?= h(url('index.php?api=quote')) ?>', 
    bookUrl: '/api/book.php' 
  };
</script>
<script src="<?= h(url('assets/booking.js')) ?>"></script>




