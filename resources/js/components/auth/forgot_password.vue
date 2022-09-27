
<template>
  <div class="content-auth">
    <button
      id="themeMode"
      type="button"
      class="
        btn btn-white
        rounded-circle
        ht-50-f
        wd-50-f
        position-absolute
        t-20
        r-20
        z-index-10
        theme-mode
        mr-0
      "
    >
      <i data-feather="moon"></i>
    </button>
    <div class="row no-gutters">
      <div class="col-lg-6">
        <div
          class="signin-hero"
          data-hero-dark="url(../assets/img/admin-signin-dark.svg)"
        ></div>
      </div>
      <div class="col-lg-6">
        <div class="sign-wrapper">
          <div class="wd-100p">
            <h3 class="mg-b-3">Forgot Password</h3>
            <p class="tx-color-03 tx-14 mg-b-40">
              Enter your registered username to receive instructions on how to
              reset your password.
            </p>

            <form v-on:submit.prevent="sendRequest()">
              <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right"
                  >Username</label
                >

                <div class="col-md-8">
                  <input
                    id="username"
                    type="text"
                    class="form-control"
                    required=""
                    v-model="form.username"
                  />
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-primary">
                    Reset Password
                  </button>
                 
                </div>
                &nbsp;
                  <a :href="back" class="btn btn-info btn-sm">Cancel</a>
              </div>
            </form>
            <div class="cms-footer mg-t-50">
              <hr />
              <p class="tx-gray-500 tx-10">
                Admin Portal v1.0 • Developed by WebFocus Solutions, Inc. 2022
              </p>
            </div>
          </div>
        </div>
        <!-- sign-wrapper -->
      </div>
    </div>
  </div>
  <!-- content -->
  <toast
    :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
  ></toast>
</template>
  
  <script>
export default {
  data() {
    return {
      back: this.$env_Url + "/",
      form: {
        username: "",
      },
    };
  },
  methods: {
    async sendRequest() {
      const res = await this.submit("post", "/auth/sendRequest", this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      console.log(res.data.msg);
      if (res.status === 200) {
        this.emailmessage(res.data.msg);
      } else {
        this.ermessage(res.data.errors);
      }
    },
  },
};
</script>
  