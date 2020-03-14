<template>
  <div class="image-manager">
    <aside class="image-manager__aside">
      <div class="image-manager__aside-search">
        <div class="form-group">
          <label for="image-manager-search">Rechercher</label>
          <input type="text" class="form-control" id="image-manager-search">
        </div>
        <div class="image-manager__aside-search-separator"></div>
      </div>
      <div class="image-manager__aside-explorer">
        <div class="image-manager__aside-explorer-title">Dossiers</div>
        <div class="image-manager__aside-explorer-folders">
          <ul class="kt-nav">
            <li class="kt-nav__item" :class="year === currentFolder ? 'kt-nav__item--active' : ''" v-for="year in sortedFolders">
              <a href="#" class="kt-nav__link" @click="changeFolder(year)">
                <i class="kt-nav__link-icon fa fa-folder-open"></i>
                <span class="kt-nav__link-text">{{ year }}</span>
                <span class="kt-nav__link-badge">
                  <span class="kt-badge kt-badge--unified-brand kt-badge--md kt-badge--rounded kt-badge--boldest">
                    {{ imageFolders[year].count }}
                  </span>
                </span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </aside>
    <div
      class="image-manager__content-container"
      :class="{'is-dragging': isDragging}"
      @dragenter="handleDragEnter"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      @dragover.prevent
    >
      <div class="image-manager__content">
        <Loader :loading="loading"/>
        <table>
          <thead>
          <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Taille</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="image in currentImages" :key="image.id">
            <td><img :src="image.url"></td>
            <td>{{ image.name }}</td>
            <td>{{ image.size }}</td>
            <td>
              <button class="btn btn-outline-hover-brand btn-sm btn-icon" @click="deleteImage(image.id)">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import Loader from './Loader';

  export default {
    name: 'image-manager',
    components: {
      Loader
    },
    props: {
      currentYear: String,
      folders: Array,
      images: Array
    },
    data() {
      return {
        imageFolders: {},
        currentFolder: this.currentYear,
        isDragging: false,
        dragCount: 0,
        loading: false,
      };
    },
    computed: {
      sortedFolders() {
        return Object.keys(this.imageFolders).sort((a, b) => (a > b ? -1 : 1));
      },
      currentImages() {
        const currentFolder = this.imageFolders[this.currentFolder];
        if (!currentFolder) {
          return [];
        }
        return this.imageFolders[this.currentFolder].children;
      }
    },
    methods: {
      async deleteImage(imageId) {
        this.loading = true;
        const response = await axios.delete(`/api/images/${imageId}`);
        if (response.status === 200) {
          const folders = JSON.parse(JSON.stringify(this.imageFolders));
          const currentFolder = folders[this.currentFolder];
          currentFolder.children = currentFolder.children.filter(image => image.id !== imageId);
          currentFolder.count = currentFolder.count - 1;
          this.imageFolders = folders;
          const currentYear = new Date().getFullYear();
          if (!currentFolder.count && currentYear !== Number(this.currentFolder)) {
            delete this.imageFolders[this.currentFolder];
            this.changeFolder(this.sortedFolders[0] || null);
          }
        }
        this.loading = false;
      },

      async changeFolder(folder) {
        if (!folder) {
          this.currentFolder = folder;
          return;
        }
        if (!this.imageFolders[folder].children.length) {
          this.loading = true;
          const response = await axios.get(`/api/images/year/${folder}`);
          if (response.status === 200) {
            this.$set(this.imageFolders[folder], 'children', response.data);
          }
          this.loading = false;
        }
        this.currentFolder = folder;
      },

      handleDragEnter(event){
        event.preventDefault();
        this.dragCount++;
        this.isDragging = true;
      },

      handleDragLeave(event){
        event.preventDefault();
        this.dragCount--;
        if (this.dragCount <= 0) {
          this.isDragging = false;
        }
      },

      handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        this.isDragging = false;
        this.dragCount = 0;
        const files = event.dataTransfer.files;
        if (files.length === 0) return;
        this.loading = true;
        files.forEach((file, index) => {
          const data = new FormData();
          data.append('file', file);
          axios.post(`/api/images`, data, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(response => {
            const image = response.data;
            this.imageFolders[image.year] = this.imageFolders[image.year] || {
              children: [],
              count: 0
            };
            this.imageFolders[image.year].children.push(image);
            this.imageFolders[image.year].count++;
            if (files.length === index + 1) {
              this.loading = false;
              if (this.currentFolder !== image.year) {
                this.changeFolder(image.year);
              }
            }
          }).catch(error => {
            if (error.response.data && error.response.data.message) {
              this.loading = false;
              toastr.error(error.response.data.message);
            }
          });
        });
      }
    },
    async created() {
      this.imageFolders = this.folders.reduce((acc, folder) => {
        acc[folder.year] = {
          count: folder.count,
          children: []
        };

        if (folder.year === this.currentFolder) {
          acc[folder.year].children = JSON.parse(JSON.stringify(this.images));
        }

        return acc;

      }, this.imageFolders);
    }
  }
</script>

<style lang="scss" scoped>
  $color-primary: #B03F3F;

  .image-manager {
    display: flex;
    height: 500px;
    overflow: hidden;

    @media screen and (max-width: 425px) {
      flex-direction: column;
    }

    .image-manager__aside {
      width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      background-color: #fff;
      border-right: 1px solid #ebedf2;
      overflow-y: auto;

      &::-webkit-scrollbar {
        width: 7px;
      }

      &::-webkit-scrollbar-track {
        background: #F7FAFB;
        padding: 1px;
      }

      &::-webkit-scrollbar-thumb {
        background: rgba($color-primary, .7);
        border-radius: 4px;

        &:hover {
          background: rgba($color-primary, 1);
        }
      }

      @media screen and (max-width: 425px) {
        width: 100%;
      }

      .image-manager__aside-search {

        label {
          font-size: 1.1rem;
          font-weight: 400;
          margin-bottom: 1rem;
        }

        .image-manager__aside-search-separator {
          height: 1px;
          width: 100%;
          background-color: #e2e5ec;
        }
      }
      .image-manager__aside-explorer {
        padding: 2rem 0;

        .image-manager__aside-explorer-title {
          font-size: 1.1rem;
          font-weight: 400;
          margin-bottom: 1rem;
        }

        .image-manager__aside-explorer-folders {
          > ul {
            list-style: none;
            margin: 0;
            padding: 0;
          }
        }
      }
    }

    .image-manager__content-container {
      position: relative;
      flex-grow: 1;
      background-color: #fff;
      display: flex;
      flex-direction: column;

      @media screen and (max-width: 425px) {
        margin-top: 1rem;
        width: 100%;
        overflow-x: auto;
      }
      
      &.is-dragging:after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba($color-primary, .6);
      }

      .image-manager__content {
        overflow-y: auto;
        padding: 20px;

        &::-webkit-scrollbar {
          width: 7px;
        }

        &::-webkit-scrollbar-track {
          background: #F7FAFB;
          padding: 1px;
        }

        &::-webkit-scrollbar-thumb {
          background: rgba($color-primary, .7);
          border-radius: 4px;

          &:hover {
            background: rgba($color-primary, 1);
          }
        }

        table {
          width: 100%;

          th {
            text-align: left;
            padding-bottom: 1rem;

            &:first-child {
              width: 250px;
            }
            &:last-child {
              text-align: right;
            }
          }

          tbody {

            tr {

              td {
                padding: 1rem 1rem 1rem 0;

                &:last-child {
                  text-align: right;
                }

                img {
                  object-fit: cover;
                  width: 250px;
                  height: 100px;
                  box-shadow: 0 1px 4px rgba(16, 43, 107, 0.6);
                  border-radius: 6px;
                }
              }
            }
          }
        }
      }

    }
  }
</style>
