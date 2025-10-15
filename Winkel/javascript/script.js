console.log("javascript loaded");

// ============================================
// REGEN ANIMATIE
// ============================================

// Instellingen voor de regen
  
// snelheid van vallen (hoe hoger, hoe sneller)
let gravity = 35;
     
// horizontale beweging van de druppels
let wind = 15;
  
// richting van de wind
let windDirection = "left";

// array om alle druppels in op te slaan
let droplets = [];  

// hoe vaak verandert de wind (in ms)
let windInterval = 100;

// Maak 100 regendruppels aan
for (let i = 0; i < 100; i++) {
  // Maak een nieuwe div voor de druppel
  let droplet = document.createElement("div");
  droplet.classList.add("droplet");
  
  // Geef de druppel een willekeurige startpositie
  let startX = Math.random() * window.innerWidth;
  let startY = Math.random() * window.innerHeight;
  droplet.style.left = `${startX}px`;
  droplet.style.top = `${startY}px`;
  
  // Voeg de druppel toe aan de rain container
  document.querySelector('.rain').appendChild(droplet);
  
  // Sla de druppel op in de array met zijn positie
  droplets.push({ el: droplet, x: startX, y: startY });
}

// Functie die de regen laat vallen
function raining() {
  // Loop door alle druppels
  for (let droplet of droplets) {
    // Verplaats de druppel naar beneden (zwaartekracht)
    droplet.y += gravity;
    
    // Verplaats de druppel horizontaal (wind)
    droplet.x += wind;
    
    // Draai de druppel op basis van de wind richting
    let rotationAngle = wind > 0 ? -wind : Math.abs(wind);
    droplet.el.style.transform = `rotate(${rotationAngle}deg)`;
    
    // Als de druppel onder de viewport komt, reset de positie naar boven
    if (droplet.y > window.innerHeight) {
      droplet.y = -20;  // start net boven het scherm
      droplet.x = Math.random() * window.innerWidth;
    }
    
    // Update de positie van de druppel op het scherm
    droplet.el.style.top = `${droplet.y}px`;
    droplet.el.style.left = `${droplet.x}px`;
  }
}

// Start de regen animatie (elke 30ms = ongeveer 33 fps)
setInterval(raining, 30);

// Functie die de wind richting verandert
function windChange() {
  // Als de wind naar links gaat, maak hem langzamer
  if (windDirection == "left") {
    wind += Math.random() - 1;
  }
  // Als de wind naar rechts gaat, maak hem sneller
  else if (windDirection == "right") {
    wind += Math.random();
  }
  
  // Keer de wind richting om als hij te sterk wordt
  if (wind < -15) {
    windDirection = "right";
  }
  else if (wind > 15) {
    windDirection = "left";
  }
}

// Update de wind
setInterval(windChange, windInterval);

// ============================================
// WISPS (LICHTGEVENDE DEELTJES) ANIMATIE
// ============================================

let wisps = [];
const wispsContainer = document.querySelector('.wisps');

// Functie die de totale hoogte van de pagina berekent
function getDocumentHeight() {
  // Kies de grootste waarde tussen verschillende height properties
  return Math.max(
    document.body.scrollHeight,
    document.body.offsetHeight,
    document.documentElement.clientHeight,
    document.documentElement.scrollHeight,
    document.documentElement.offsetHeight
  );
}

// Functie om een nieuwe wisp te maken
function createWisp() {
  const wisp = document.createElement('div');
  wisp.classList.add('wisp');
  
  // Geef de wisp een willekeurige grootte tussen 5 en 15 pixels
  const size = 5 + Math.random() * 10;
  wisp.style.width = `${size}px`;
  wisp.style.height = `${size}px`;
  
  // FIXED: Gebruik de volledige document hoogte in plaats van alleen window hoogte
  // Hierdoor kunnen wisps ook onder de fold verschijnen als de pagina scrollbaar is
  const documentHeight = getDocumentHeight();
  wisp.style.left = `${Math.random() * window.innerWidth}px`;
  wisp.style.top = `${Math.random() * documentHeight}px`;
  
  // Geef de wisp een glow effect
  wisp.style.boxShadow = `0 0 ${size * 0.5}px ${size * 0.5}px rgba(255, 231, 121, 0.8)`;
  wisp.style.filter = 'blur(4px)';
  
  // Start onzichtbaar voor een smooth fade-in effect
  wisp.style.opacity = '0';
  wisp.style.transition = 'opacity 1.5s ease-in';
  
  // Voeg de wisp toe aan de container
  wispsContainer.appendChild(wisp);
  
  // Fade in na 100ms
  setTimeout(() => wisp.style.opacity = '1', 100);
  
  // Verwijder de wisp na 3-5 seconden met een fade-out
  setTimeout(() => {
    wisp.style.transition = 'opacity 1s ease-out';
    wisp.style.opacity = '0';
    // Verwijder het element helemaal na de fade-out
    setTimeout(() => wisp.remove(), 1000);
  }, 3000 + Math.random() * 2000);
}

// Maak elke 500ms een nieuwe wisp (2 per seconde)
setInterval(createWisp, 500);

// Functie om de wisps container hoogte bij te werken
function updateWispsHeight() {
  const documentHeight = getDocumentHeight();
  wispsContainer.style.height = `${documentHeight}px`;
}

// Set de initiële hoogte meteen
updateWispsHeight();

// Update de hoogte als het venster van grootte verandert
window.addEventListener('resize', updateWispsHeight);

// Update de hoogte ook als de pagina geladen is (voor dynamische content)
window.addEventListener('load', updateWispsHeight);

// Dit zorgt ervoor dat wisps ook werken als content later wordt toegevoegd
setInterval(updateWispsHeight, 2000);