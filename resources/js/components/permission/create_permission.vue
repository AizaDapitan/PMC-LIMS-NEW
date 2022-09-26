
<template>
  <div class="container-fluid pd-x-0">
    <div
      class="
        d-sm-flex
        align-items-center
        justify-content-between
        mg-b-20 mg-lg-b-25 mg-xl-b-30
      "
    >
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-style1 mg-b-5">
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">LIMS</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">Permissions</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Add New Permission
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Add New Permission</h4>
      </div>
    </div>
    <div v-if="errors_exist" class="alert alert-danger" role="alert">
      Whoops! Something didn't work.
      <ul>
        <div v-for="error in errors" :key="error.id">
          <li>{{ error[0] }}</li>
        </div>
      </ul>
    </div>
    <div class="row row-sm">
      <div class="col-lg-6">
        <div class="form-group">
          <label class="d-block">Status <i class="text-danger">*</i></label>
          <input
            type="checkbox"
            id="statusValue"
            @click="storeValue(statusValue)"
            checked
          />
          <span>&nbsp;</span>
          <label for="statusValue">
            <span>Active</span>
          </label>
        </div>

        <div class="form-group">
          <label class="d-block">Module <i class="text-danger">*</i></label>
          <select class="custom-select" @change="getData($event)">
            <option selected>Select Module</option>
            <option
              v-for="module in modules"
              :key="module.description"
              :value="module.description"
            >
              {{ module.description }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label class="d-block"
            >Permission Description <i class="text-danger">*</i></label
          >
          <textarea
            class="form-control"
            rows="3"
            v-model="form.description"
            placeholder="Permission Description"
            maxlength="190"
          ></textarea>
        </div>
      </div>

      <div class="col-lg-12 mg-t-30">
        <button
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="createPermission"
        >
          <i data-feather="save" class="mg-r-5"></i> Save
        </button>
        <a
          :href="dashboard"
          class="btn btn-white tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          ><i data-feather="arrow-left" class="mg-r-5"></i> Back to Dashboard</a
        >
      </div>
    </div>
    <div class="cms-footer mg-t-50">
      <hr />
      <p class="tx-gray-500 tx-10">
        Admin Portal v1.0 • Developed by WebFocus Solutions, Inc. 2022
      </p>
    </div>
  </div>
  <toast
    :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
  ></toast>
</template>

<script  type="application/javascript">
export default {
  data() {
    return {
      errors_exist: false,
      errors: {},
      dashboard: this.$env_Url + "/permissions/dashboard",
      form: {
        description: "",
        status: false,
      },
      modules: [],
    };
  },

  mounted() {
    this.fetchModules();
  },

  methods: {
    getData(event) {
      this.form.module_type = event.target.value;
    },

    async createPermission() {
      const res = await this.submit("post", "/permissions/store", this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.smessage();
        window.location.href = this.$env_Url + "/permissions/dashboard";
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
      }
    },

    async fetchModules() {
      const res = await this.callApi("get", "/permissions/modulesList");
      this.modules = res.data;
    },

    storeValue(statusValue) {
      if (document.getElementById("statusValue").checked) {
        this.form.status = 1;
      } else {
        this.form.status = 0;
      }
    },
  },
};
</script>
