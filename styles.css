@font-face {
    font-family: 'Joliet';
    src: url('Joliet-Regular.eot');
    src: local('Joliet Regular'), local('Joliet-Regular'),
        url('fontes/Joliet-Regular.eot?#iefix') format('embedded-opentype'),
        url('fontes/Joliet-Regular.woff2') format('woff2'),
        url('fontes/Joliet-Regular.woff') format('woff'),
        url('fontes/Joliet-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

@font-face {
  font-family: 'TheSeasons';
  src: url('fontes/the-seasons-regular.ttf') format('truetype');
  font-weight: normal;
  font-style: normal;
  font-display: swap; /* ajuda na performance */
}


body {
  margin: 0;
  padding: 0;
  background-color: #fdf8ee;
  color: #1a1a1a;
  font-family: 'Marcellus', serif;
}

.container {
  text-align: center;
  padding: 10px 20px;
}

.title {
  font-family: 'Joliet', serif;
  font-size: 12rem;
  font-weight: 400;
  margin-bottom: 100px;
}

.image-wrapper {
  display: flex;
  justify-content: center;
}

.photo {
  max-width: 100%;
  width: 600px;
  height: auto;
}

.footer {
  margin-top: 150px;
  display: flex;
  justify-content: space-between;
  font-size: 1.2rem;
  padding: 0 200px;
  margin-bottom: 50px;
}

.left, .right {
  color: #1a1a1a;
}


.section-ilustracao {
  display: flex;
  align-items: center;
  background-color: #fdf8ee;
  margin: 0;
  padding: 0;
}

.image-side {
  flex: 1;
}

.desenho {
  width:40%;
  height: auto;
  object-fit: cover;
  display: block;
  margin: 0;
  padding: 0;
}

.countdown-side {
  padding: 0px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0px;
  margin-right: 70px;
}

.count-item {
  font-family: "Libertinus Serif Display", system-ui;
  font-size: 10rem;
  color: #1a1a1a;
  transition: transform 0.3s;
  align-items: center;
  justify-content: center;
}

/* Zig-zag vertical */
.count-item:nth-child(odd) {
  transform: translateX(-30px); /* sobe 10px */
}

.count-item:nth-child(even) {
  transform: translateX(30px); /* desce 10px */
}

.count-item small {
  display: block;
  font-size: 2rem;
  color: #555;
  text-transform: lowercase;
  justify-content: center;
  align-items: center;
}


.local {
  background-color: #fdf8ee;
  padding: 40px 0;
}

.local-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.local-header h2 {
  font-size: 10.5rem;
  font-family: 'Joliet', serif;
  font-weight: 400;
  margin: 0 100px 30px 100px;
}

.local-header img {
  width: 30px; /* ajuste conforme necessário */
  height: auto;
}

.local-gallery{
  display: flex;
  gap: 50px;
  justify-content: center;
}

.local-gallery a {
  display: block;
  width: 30%;
  transition: transform 0.7s ease;
}

.local-gallery img {
  width: 100%;
  object-fit: cover;
  height: 834px;
  filter: brightness(0.76) contrast(1) saturate(0);
  transition: transform 0.3s ease, filter 0.3s ease;
  margin-bottom: 100px;
  transition: transform 0.5s ease;
}

/* Aumenta suavemente quando passa o mouse */
.local-gallery a:hover img {
  transform: scale(1.1);
  filter: brightness(1) contrast(1) saturate(1); /* volta à cor normal */
}


.mapa iframe {
  width: 350px;
  height: 180px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  filter: brightness(0.76) contrast(1) saturate(0.6);
  margin-right: 50px;
}

.cronograma-header {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 80px;
}

.cronograma-header h2 {
  font-size: 10.5rem;
  font-family: 'Joliet', serif;
  font-weight: 400;
  margin: 0 100px 30px 100px;
}


.timeline {
  position: relative;
  max-width: 1500px;
  margin: auto;
  padding: 20px 0;
  font-family: 'Marcellus', serif;
}

.timeline h3{
  font-size: 2rem;
  font-family: 'Marcellus', serif;

}

.timeline p{
  font-size: 1.4rem;
  font-family: 'Marcellus', serif;

}


.timeline::after {
  content: '';
  position: absolute;
  width: 4px;
  background-color: #000000;
  top: 0;
  bottom: 0;
  left: 50%;
  margin-left: -2px;
}

.timeline-event {
  padding: 10px 40px;
  position: relative;
  width: 50%;
  opacity: 0;
  transform: translateY(50px);
  transition: all 0.6s ease-out;
}

.timeline-event.left {
  left: -10%;
}

.timeline-event.right {
  left: 50%;
}


.timeline-event .content {
  padding: 20px;
  position: relative;
  
}

.timeline-event .content-actual{
  padding: 20px;
  position: relative;
  border: 1px solid #000000;
  
}

.timeline-event.left .content::after {
  content: '';
  position: absolute;
  top: 20px;
  right: -10px;
  border-width: 10px;
  border-style: solid;
  border-color: transparent transparent transparent #fff;
}

.timeline-event.right .content::after {
  content: '';
  position: absolute;
  top: 20px;
  left: -10px;
  border-width: 10px;
  border-style: solid;
  border-color: transparent #fff transparent transparent;
}

.timeline-event.active {
  opacity: 1;
  transform: translateY(0);
}
