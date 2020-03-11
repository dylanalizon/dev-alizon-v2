<template>
  <div class="kt-pagination  kt-pagination--brand mt-auto">
    <ul class="kt-pagination__links">
      <li class="kt-pagination__link--first" :class="pagination.current.value === pagination.first.value ? 'kt-pagination__link--disabled' : ''">
        <a :href="pagination.first.url"><i class="fa fa-angle-double-left kt-font-brand"></i></a>
      </li>
      <li class="kt-pagination__link--prev" :class="!pagination.previous ? 'kt-pagination__link--disabled' : ''">
        <a :href="pagination.previous ? pagination.previous.url : '#'"><i class="fa fa-angle-left kt-font-brand"></i></a>
      </li>
      <li v-if="pagination.previous">
        <a :href="pagination.previous.url">{{ pagination.previous.value }}</a>
      </li>
      <li class="kt-pagination__link--active">
        <a href="#">{{ pagination.current.value }}</a>
      </li>
      <li v-if="pagination.next">
        <a :href="pagination.next.url">{{ pagination.next.value }}</a>
      </li>
      <li class="kt-pagination__link--next" :class="!pagination.next ? 'kt-pagination__link--disabled' : ''">
        <a :href="pagination.next ? pagination.next.url : '#'"><i class="fa fa-angle-right kt-font-brand"></i></a>
      </li>
      <li class="kt-pagination__link--last" :class="pagination.current.value === pagination.last.value ? 'kt-pagination__link--disabled' : ''">
        <a :href="pagination.last.url"><i class="fa fa-angle-double-right kt-font-brand"></i></a>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'pagination',
    props: {
      pagination: Object
    }
  }
</script>
