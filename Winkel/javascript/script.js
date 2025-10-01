console.log("javascript loaded");

let gravity = 35; // speed of falling
let wind = 15;    // horizontal movement
let windDirection = "left";
let droplets = [];

// create droplets
for (let i = 0; i < 100; i++) {
  let droplet = document.createElement("div");
  droplet.classList.add("droplet");

  let startX = Math.random() * window.innerWidth;
  let startY = Math.random() * window.innerHeight;

  droplet.style.left = `${startX}px`;
  droplet.style.top = `${startY}px`;

  document.querySelector('.rain').appendChild(droplet);

  droplets.push({ el: droplet, x: startX, y: startY });
}

function raining() {
  for (let droplet of droplets) {
    droplet.y += gravity;
    droplet.x += wind;

    let rotationAngle = wind > 0 ? -wind : Math.abs(wind);
    droplet.el.style.transform = `rotate(${rotationAngle}deg)`;


    if (droplet.y > window.innerHeight) {
      droplet.y = -20;
      droplet.x = Math.random() * window.innerWidth;
    }

    droplet.el.style.top = `${droplet.y}px`;
    droplet.el.style.left = `${droplet.x}px`;
  }
}

setInterval(raining, 30);

function windChange() {
  if (windDirection == "left") {
    wind += Math.random() - 1;
  }
  else if (windDirection == "right") {
    wind += Math.random();
  }
  if (wind < -15) {
    windDirection = "right";
  }
  else if (wind > 15) {
    windDirection = "left";
  }

}

setInterval(windChange, 100);

// Wisps
let wisps = [];
const wispsContainer = document.querySelector('.wisps');

function createWisp() {
  const wisp = document.createElement('div');
  wisp.classList.add('wisp');
  
  const size = 5 + Math.random() * 10;
  wisp.style.width = `${size}px`;
  wisp.style.height = `${size}px`;
  wisp.style.left = `${Math.random() * window.innerWidth}px`;
  wisp.style.top = `${Math.random() * window.innerHeight}px`;
  wisp.style.boxShadow = `0 0 ${size * 0.5}px ${size * 0.5}px rgba(255, 231, 121, 0.8)`;
  wisp.style.filter = 'blur(4px)';
  wisp.style.opacity = '0';
  wisp.style.transition = 'opacity 1.5s ease-in';
  wisp.style.transform.rotateZ = "0";



  wispsContainer.appendChild(wisp);
  
  // Fade in smoothly
  setTimeout(() => wisp.style.opacity = '1', 100);

  let randomDirection = Math.floor() * 360;
  setTimeout(() => wisp.style.left = randomDirection)

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