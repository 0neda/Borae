import "./app.css";
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import focus from '@alpinejs/focus'

window.Alpine = Alpine;
Alpine.plugin(mask);
Alpine.plugin(focus)
Alpine.start();

console.log("Boraê Inicializado!");
