import './bootstrap';
import { createApp } from 'vue';
import { registerPlugins } from './utils/plugins';
import App from './App.vue';
import 'vue-sonner/style.css'

const app = createApp(App);

registerPlugins(app);

app.mount('#app');
