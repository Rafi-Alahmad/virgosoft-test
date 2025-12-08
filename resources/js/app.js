import './bootstrap';
import { createApp } from 'vue';
import { registerPlugins } from './utils/plugins';
import App from './App.vue';

const app = createApp(App);

registerPlugins(app);

app.mount('#app');
