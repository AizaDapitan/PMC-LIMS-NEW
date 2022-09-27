
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
                <a :href="dashboard">Analyst</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Create
              </li>
            </ol>
          </nav>
          <h4 class="mg-b-0 tx-spacing--1">Create New Analyst</h4>
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
            <label class="d-block">Name <i class="text-danger">*</i></label>
            <input
              type="text"
              class="form-control"
              v-model="form.name"
              placeholder="Name"
              maxlength="150"
            />
          </div>
  
          <div class="form-group">
            <label class="d-block">Description <i class="text-danger">*</i></label>
            <textarea
              class="form-control"
              rows="3"
              v-model="form.description"
              placeholder="Description"
              maxlength="190"
            ></textarea>
          </div>
        </div>
  
        <div class="col-lg-12 mg-t-30">
          <button
            class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
            @click.prevent="createRole"
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
        dashboard: this.$env_Url + "/qaanalysts/dashboard",
        form: {
          name: "",
          description: ""
        },
      };
    },
  
    methods: {
      async createRole() {
        const res = await this.submit("post", "/qaanalysts/store", this.form, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        });
        if (res.status === 200) {
          this.smessage();
          window.location.href = this.$env_Url + "/qaanalysts/dashboard";
        } else {
          this.errors_exist = true;
          this.errors = res.data.errors;
        }
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
  