<template>
  <div class="input-image-manager-container">
    <div class="input-image-manager" @click="openDialog"></div>
    <div class="modal fade" tabindex="-1" role="dialog" ref="modal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Image manager</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <ImageManager
            v-if="isOpen"
            :current-year="currentYear"
            :folders="folders"
            :images="images"
          />
        </div>
      </div>
    </div>
    <Loader :loading="loading"/>
  </div>
</template>

<script>
  import ImageManager from "./ImageManager";
  import Loader from "./Loader";
  import axios from "axios";

  export default {
    name: 'input-image-manager',
    components: {
      ImageManager,
      Loader
    },
    data() {
      return {
        isOpen: false,
        folders: [],
        currentYear: '',
        images: [],
        loading: false
      }
    },
    mounted() {
      $(this.$refs.modal).on('hidden.bs.modal', () => {
        this.isOpen = false;
      })
    },
    methods: {
      async openDialog() {
        this.loading = true;
        let response = await axios.get(`/api/images/folders`);
        this.folders = response.data;
        this.currentYear = this.folders[0].year;
        response = await axios.get(`/api/images/year/${this.currentYear}`);
        this.images = response.data;
        this.loading = false;
        this.isOpen = true;
        $(this.$refs.modal).modal();
      }
    }
  }
</script>

<style lang="scss" scoped>
  $color-primary: #B03F3F;

  .input-image-manager-container {
    position: relative;
    .input-image-manager {
      position: relative;
      width: 100%;
      height: 100%;
      min-height: 100px;
      border: 1px solid #e2e5ec;
      cursor: pointer;
      transition: .2s;

      &:hover {
        box-shadow: 0px 0px 3px $color-primary;
      }

      &::after {
        content:'';
        position: absolute;
        z-index: 1;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        opacity: .06;
        background: url(../../../images/upload.svg) center center / 100% 100% no-repeat;
      }

      .modal {
        .modal-header {
          background: #fff;
        }
      }
    }
  }
</style>
