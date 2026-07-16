// Airport Taxi Limburg booking flow
(function () {
  const cfg = window.__ATL || {};
  const qForm = document.getElementById('quoteForm');
  const bForm = document.getElementById('bookForm');
  const qResult = document.getElementById('quoteResult');
  const qJson = document.getElementById('quoteJson');
  const bookBtn = document.getElementById('bookBtn');
  const editBtn = document.getElementById('editQuote');
  
  // TOEVOEGING: Definieer returnFields hier zodat de functie hem vindt
  const returnFields = document.getElementById('returnFields');

  // FUNCTIE: Update de zichtbaarheid en validatie
  function updateReturnFields() {
      const selected = qForm.querySelector('input[name="trip_type"]:checked');
      if (!selected || !returnFields) return;

      const isReturn = selected.value === 'return';
      
      // Toon of verberg container
      returnFields.style.setProperty('display', isReturn ? 'grid' : 'none', 'important');

      // Update 'required' voor alle inputs in die container
      returnFields.querySelectorAll('input').forEach(input => {
          input.required = isReturn;
      });
  }

  // 1. Voer uit direct bij het laden
  updateReturnFields();

  // SELECTEER DE LABELS EN SELECTS
  const labelRegion = document.getElementById('regionField');
  const labelAirport = document.getElementById('airportField');
  const selectRegion = labelRegion ? labelRegion.querySelector('select') : null;
  const selectAirport = labelAirport ? labelAirport.querySelector('select') : null;

  if (!qForm || !selectRegion || !selectAirport) return;

  // Sla de originele PHP-opties op in het geheugen bij het laden van de pagina
  const htmlRegioOpties = selectRegion.innerHTML;
  const htmlLuchthavenOpties = selectAirport.innerHTML;

  function fmt(v) { return '€ ' + Number(v).toFixed(2).replace(/\./, ','); }

  // WISSEL FUNCTIE
  function switchLabels(direction) {
      const spanRegio = document.getElementById('label-text-regio');
      const spanAirport = document.getElementById('label-text-airport');
      
      if (direction === 'to_home') {
          if (spanRegio) spanRegio.textContent = 'Van (Luchthaven)';
          if (spanAirport) spanAirport.textContent = 'Naar (Regio)';
          selectRegion.innerHTML = htmlLuchthavenOpties;
          selectAirport.innerHTML = htmlRegioOpties;
          selectRegion.name = 'airport_id';
          selectAirport.name = 'region_id';
      } else {
          if (spanRegio) spanRegio.textContent = 'Regio';
          if (spanAirport) spanAirport.textContent = 'Luchthaven';
          selectRegion.innerHTML = htmlRegioOpties;
          selectAirport.innerHTML = htmlLuchthavenOpties;
          selectRegion.name = 'region_id';
          selectAirport.name = 'airport_id';
      }
  }

  qForm.addEventListener('change', (e) => {
    if (e.target.name === 'direction') {
      switchLabels(e.target.value);
    }
    
    if (e.target.name === 'trip_type') {
      updateReturnFields();
    }
  });

  // INITIALISATIE
  const initialDirection = qForm.querySelector('input[name="direction"]:checked');
  if (initialDirection) {
      switchLabels(initialDirection.value);
  }

  
  // --- Submit/booking code ---
  qForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(qForm));
    qResult.hidden = false;
    qResult.className = 'quote-result';
    qResult.innerHTML = 'Berekenen…';
    try {
      const res = await fetch(cfg.quoteUrl, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });
      const j = await res.json();
      if (!j.ok) {
        qResult.className = 'quote-result err';
        qResult.innerHTML = '<strong>' + (j.error || 'Kon geen prijs berekenen.') + '</strong>';
        return;
      }
      const q = j.quote;
      const trip = q.trip_type === 'return' ? 'retour' : 'enkele reis';
      const veh = q.vehicle === 'van' ? 'Taxibus' : 'Personenauto';
      qResult.innerHTML = 'Vaste ritprijs (' + trip + ', ' + veh + ') <strong>' + fmt(q.price) + '</strong>';
      qJson.value = JSON.stringify(data);
      qForm.hidden = true;
      bForm.hidden = false;
      bForm.style.display = 'block';
    } catch (err) {
      qResult.className = 'quote-result err';
      qResult.innerHTML = '<strong>Fout bij ophalen prijs.</strong>';
    }
  });

  editBtn && editBtn.addEventListener('click', () => {
    bForm.hidden = true;
    qForm.hidden = false;
  });

  bForm && bForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // 1. Verzamel de data uit het formulier op een veilige manier
    const formData = new FormData(bForm);
    const bookingData = {};
    
    // Dit zorgt ervoor dat ook velden zonder 'name' (als je die per ongeluk hebt) 
    // of velden met specifieke namen correct in het object komen
    for (let [key, value] of formData.entries()) {
        bookingData[key] = value;
    }

    // Handmatige fallback: als de naam nog steeds niet gevonden is, zoek op ID
    if (!bookingData.customer_name) {
        bookingData.customer_name = document.getElementById('customer_name')?.value || '';
    }
    if (!bookingData.customer_phone) {
        bookingData.customer_phone = document.getElementById('customer_phone')?.value || '';
    }

    const quoteData = qJson.value ? JSON.parse(qJson.value) : {};
    const payload = { ...quoteData, ...bookingData };
    
    console.log("Verzonden payload:", payload); // <-- CHECK DIT IN JE F12 CONSOLE

    bookBtn.disabled = true; bookBtn.textContent = 'Bezig met boeken…';
    
    try {
        const res = await fetch(cfg.bookUrl, {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const j = await res.json();
        
        if (!j.ok) {
            console.error("Server fout:", j);
            alert(j.error || 'Boeking mislukt.');
            bookBtn.disabled = false; 
            bookBtn.textContent = 'Boeking bevestigen';
            return;
        }
        window.location.href = j.redirect;
    } catch (err) {
        console.error("Fetch fout:", err);
        alert('Fout bij verzenden boeking.');
        bookBtn.disabled = false; 
        bookBtn.textContent = 'Boeking bevestigen';
    }
});
})();