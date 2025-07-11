import './bootstrap';

console.log('app.js start'); // 追加

import Alpine from 'alpinejs';

console.log('Alpine imported:', Alpine); // 追加

window.Alpine = Alpine;

console.log('window.Alpine set:', window.Alpine); // 追加

Alpine.start();

console.log('Alpine started.'); // 追加