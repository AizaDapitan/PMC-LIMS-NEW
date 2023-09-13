<template>
  <div v-if="errors_exist" class="alert alert-danger" role="alert">
    Whoops! Something didn't work.
    <ul>
      <div v-for="error in errors" :key="error.id">
        <li>{{ error[0] }}</li>
      </div>
    </ul>
  </div>
  <form role="form" method="GET" action="">
    <div class="modal-body">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label
              >Lab Batch<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              class="form-control input-sm"
              v-model="form.labbatch"
              disabled="true"
            />
          </div>
        </div>
        <div class="col-lg-12"></div>
        <div class="col-lg-12">
          <div class="form-group">
            <label
              >Analyze by<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <select class="custom-select tx-base" v-model="form.analyzedBy">
              <option
                v-for="supervisor in supervisors"
                :key="supervisor.id"
                :value="supervisor.name"
              >
                {{ supervisor.name }}
              </option>
            </select>
          </div>
        </div>
        <div class="col-lg-12"></div>
        <div class="col-lg-12">
          <div class="form-group">
            <label
              >Checked and Verified by<span
                class="text-danger"
                aria-required="true"
              >
                *
              </span></label
            >

            <select class="custom-select tx-base" v-model="form.verifiedBy">
              <option
                v-for="chemist in chemists"
                :key="chemist.id"
                :value="chemist.name"
              >
                {{ chemist.name }}
              </option>
            </select>
          </div>
        </div>
        <div v-if="!this.form.isAssayer" class="col-lg-12">
          <div class="form-group">
            <label for="type"
              >Type<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <select
              class="custom-select tx-base"
              id="type"
              name="type"
              v-model="form.transmittalType"
              :disabled="true"
            >
              <option
                value="Rock"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Rock
              </option>
              <option
                value="Carbon"
                v-if="reportType == 'Generate Certificate of Analysis'"
              >
                Carbon
              </option>
              <option
                value="Bulk"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Bulk
              </option>
              <option
                value="Cut"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Cut
              </option>
              <option
                value="Mine Drill"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Mine Drill
              </option>
              <option
                value="Solids"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Solids
              </option>
              <option
                value="Solutions"
                v-if="reportType == 'Generate Analytical Result'"
              >
                Solutions
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button
        type="reset"
        class="btn btn-white tx-13 btn-uppercase"
        @click.prevent="closeDialog"
      >
        <i data-feather="x-circle" class="mg-r-5"></i> Cancel
      </button>
      <button
        type="submit"
        class="btn btn-primary tx-13 btn-uppercase"
        @click.prevent="generate"
      >
        <i data-feather="save" class="mg-r-5"></i> Generate
      </button>
    </div>
  </form>
  <toast
    :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
  ></toast>
</template>
<script>
export default {
  inject: ["dialogRef"],
  data() {
    return {
      errors_exist: false,
      errors: {},
      reportType: this.dialogRef.data.reportType,
      supervisors: [],
      chemists: [],
      form: {
        labbatch: this.dialogRef.data.labbatch,
        analyzedBy: "",
        verifiedBy: "",
        transmittalType: this.dialogRef.data.transType,
      },
    };
  },
  created() {
    this.fetchSupervisor();
    this.fetchChemist();
  },
  methods: {
    closeDialog() {
      this.dialogRef.close();
    },
    async generate() {
      if (this.form.analyzedBy == "") {
        this.errors = {
          error: ["The field Analyzed By is required!"],
        };
        this.errors_exist = true;
      } else if (this.form.verifiedBy == "") {
        this.errors = {
          error: ["The field Checked and Verified By is required!"],
        };
        this.errors_exist = true;
      } else if (this.form.transmittalType == "") {
        this.errors = {
          error: ["The field Transmittal Type is required!"],
        };
        this.errors_exist = true;
      } else {
        var data =
          this.form.analyzedBy +
          "*" +
          this.form.verifiedBy +
          "*" +
          this.form.transmittalType +
          "*" +
          this.form.labbatch
          ;
        window.location.href =
          this.$env_Url + "/analyst/AnalyticalResult/" + data;
      }
    },
    async fetchSupervisor() {
      const res = await this.getDataFromDB(
        "get",
        "/supervisors/getSupervisorActive"
      );

      this.supervisors = res.data;
    },
    async fetchChemist() {
      const res = await this.getDataFromDB(
        "get",
        "/chiefchemists/getChiefChemistActive"
      );

      this.chemists = res.data;
    },
  },
};
</script>