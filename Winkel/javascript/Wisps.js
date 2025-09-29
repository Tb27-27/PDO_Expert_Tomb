// Wisps
let wisps = [];
const wispsContainer = document.querySelector('.wisps');

function createWisp() {
  const wisp = document.createElement('div');
  wisp.classList.add('wisp');
  
  const size = 10 + Math.random() * 10;
  wisp.style.width = `${size}px`;
  wisp.style.height = `${size}px`;
  wisp.style.left = `${Math.random() * window.innerWidth}px`;
  wisp.style.top = `${Math.random() * window.innerHeight}px`;
  wisp.style.boxShadow = `0 0 ${size * 4}px ${size * 3}px rgba(255, 208, 0, 0.8)`;
  wisp.style.filter = 'blur(8px)';
  wisp.style.opacity = '0';
  wisp.style.transition = 'opacity 1.5s ease-in';
  
  wispsContainer.appendChild(wisp);
  
  // Fade in smoothly
  setTimeout(() => wisp.style.opacity = '1', 100);
  
  // Remove after 3-5 seconds
  setTimeout(() => {
    wisp.style.transition = 'opacity 1s ease-out';
    wisp.style.opacity = '0';
    setTimeout(() => wisp.remove(), 1000);
  }, 3000 + Math.random() * 2000);
}

// Create wisps every 0.5 seconds (faster)
setInterval(createWisp, 500);

// Create initial batch
for (let i = 0; i < 12; i++) {
  setTimeout(createWisp, i * 200);
}