import { loadVueComponent } from "./helpers";

loadVueComponent('image-manager', () => import('./components/ImageManager'))
loadVueComponent('input-image-manager', () => import('./components/InputImageManager.vue'))
