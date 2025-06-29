import "./app.css";
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

window.Alpine = Alpine;
Alpine.plugin(mask);
console.log("Plugin mask registrado:", Alpine);
Alpine.start();

console.log("Happy coding !!");
