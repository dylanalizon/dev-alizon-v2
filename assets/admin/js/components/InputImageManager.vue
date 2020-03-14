<template>
  <div class="input-image-manager-container" :class="{'has-preview': value}">
    <input type="hidden" :name="dataName" :value="value">
    <div v-if="!value" class="input-image-manager" @click="openDialog"/>
    <template v-else>
      <img :src="url" class="input-image-manager-preview" @click="openDialog"/>
      <div class="input-image-manager-remove" @click="removeImage">
      </div>
    </template>
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
            :with-choice="true"
            @input="handleInput"
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
    props: {
      dataName: {
        type: String,
        required: true
      },
      dataValue: Number,
      dataUrl: String
    },
    data() {
      return {
        isOpen: false,
        folders: [],
        currentYear: '',
        images: [],
        loading: false,
        value: null,
        url: null
      }
    },
    mounted() {
      if (this.dataValue !== undefined) {
        this.value = this.dataValue;
      }
      if (this.dataUrl !== undefined) {
        this.url = this.dataUrl;
      }

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
      },

      handleInput(value, url) {
        this.value = value;
        this.url = url;
        $(this.$refs.modal).modal('hide');
        this.isOpen = false;
      },

      removeImage() {
        this.value = null;
        this.url = null;
      }
    }
  }
</script>

<style lang="scss" scoped>
  $color-primary: #B03F3F;

  .input-image-manager-container {
    position: relative;

    &.has-preview {
      display: inline-block;
    }

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

    .input-image-manager-preview {
      border: 1px solid #e2e5ec;
      cursor: pointer;
      transition: .2s;

      &:hover {
        box-shadow: 0px 0px 3px $color-primary;
      }
    }

    .input-image-manager-remove {
      position: absolute;
      right: 0;
      top: 0;
      height: 1.5rem;
      width: 1.5rem;
      text-align: center;
      background: $color-primary;
      color: #fff;
      font-family: "LineAwesome";
      text-decoration: inherit;
      text-rendering: optimizeLegibility;
      text-transform: none;
      -webkit-font-smoothing: antialiased;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color .3s ease-in-out;
      border-radius: 50%;
      transform: translate(30%, -30%);

      &:hover {
        background: darken($color-primary, 10%);
      }

      &::before {
        font-size: 1.3rem;
        content: "";
      }
    }
  }
</style>
