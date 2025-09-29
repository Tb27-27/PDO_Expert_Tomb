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



// Fairies

let fairies = [];

function randomFairies() {
  for (let i = 0; i < 14; i++) {
    let fairy = document.createElement("div");
    fairy.classList.add("fairy");

    let startX = Math.random() * window.innerWidth;
    let startY = Math.random() * window.innerHeight;

    fairy.style.left = `${startX}px`;
    fairy.style.top = `${startY}px`;

    document.querySelector('.fairies').appendChild(fairy);

    fairies.push({ el: fairy, x: startX, y: startY });
  }
};

randomFairies();

setInterval(randomFairies, 3000);

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
