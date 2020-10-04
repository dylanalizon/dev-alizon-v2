import Sortable from 'sortablejs';
import axios  from 'axios';
import '../css/style.scss';
import './theme';
import { loadVueComponent } from "./helpers";

loadVueComponent('image-manager', () => import('./components/ImageManager'))
loadVueComponent('input-image-manager', () => import('./components/InputImageManager.vue'))

// Sortable table (TimelineItem)
if (document.querySelector('.table-sortable')) {
  Sortable.create(document.querySelector('.table-sortable tbody'), {
    draggable: '.sortable-item',
    handle: '.sortable-handler',
    animation: 150,
    async onEnd(event) {
      const position = event.newIndex;
      const oldPosition = event.oldIndex;
      if (position === oldPosition) {
        return;
      }
      const id = event.item.dataset.id;
      try {
        await axios.put(`/api/timeline_items/${id}`, {
          position
        });
      } catch (e) {
        if (e.response.data && e.response.data.message) {
          toastr.error(e.response.data.message);
        }
      }
    }
  });
}
