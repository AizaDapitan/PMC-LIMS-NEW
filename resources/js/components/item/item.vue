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
            <label for="transmittal-no">Transmittal No.</label>
            <input
              class="form-control input-sm"
              v-model="form.transmittalno"
              disabled="true"
            />
          </div>
        </div>
        <div class="col-lg-12"></div>
        <div class="col-lg-12">
          <div class="form-group">
            <label for="sample-no" v-if="!this.form.isAssayer"
              >Sample No.<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <label for="sample-no" v-else
              >Sample Code<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              class="form-control input-sm"
              id="sample-no"
              name="sample-no"
              v-model="form.sampleno"
              :disabled="this.isdisabled"
              style="color: black; background-color: white;"
            />
          </div>
        </div>
        <div v-if="!this.form.isAssayer" class="col-lg-12">
          <div class="form-group">
            <label for="drescription"
              >Description<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <textarea
              class="form-control input-sm"
              id="drescription"
              name="drescription"
              v-model="form.description"
              :disabled="!this.isDeptUser"
              style="color: black; background-color: white;"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="elements"
              >Elements<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="text"
              class="form-control input-sm"
              id="elements"
              name="elements"
              v-model="form.elements"
              :disabled="!this.isDeptUser"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="method-code"
              >Method Code<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="text"
              class="form-control input-sm"
              id="method-code"
              name="method-code"
              v-model="form.methodcode"
              :disabled="!this.isDeptUser"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="comments">Comments</label>
            <textarea
              class="form-control input-sm"
              id="comments"
              name="comments"
              v-model="form.comments"
              :disabled="!this.isDeptUser"
              style="color: black; background-color: white;"
            ></textarea>
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Sample Wt./Volume<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.samplewtvolume"
              :disabled="!this.isReceiving"
              style="color: black; background-color: white;"
            />
          </div>
        </div>
        <div v-if="this.isAnalystAssayer" class="col-lg-12">
          <div class="form-group">
            <label for="source">Source</label>
            <input
              class="form-control input-sm"
              v-model="form.source"
              disabled="true"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Sample Weight (Grams)<span
                class="text-danger"
                aria-required="true"
              >
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.samplewtgrams"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Crusible Used<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              class="form-control input-sm"
              v-model="form.crusibleused"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <h4 style="color: black;">Elements</h4>
          <div class="form-group">
            <label for="sample-no"
              >Flux (Grams)<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.fluxg"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Flour (Grams)<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.flourg"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Niter (Grams)<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.niterg"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Lead (Grams)<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.leadg"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div class="form-group">
            <label for="sample-no"
              >Silican (Grams)<span class="text-danger" aria-required="true">
                *
              </span></label
            >
            <input
              type="number"
              class="form-control input-sm"
              v-model="form.silicang"
              :disabled="!this.form.isAssayer"
              style="color: black; background-color: white;"
            />
          </div>
          <div v-if="this.form.isAnalyst">
            <div>
              <hr class="mg-t-10 mg-b-30" />
            </div>
            <h5 style="color: black;" class="mb-4">{{ this.methodheader }}</h5>
            <h6 v-if="this.isfa30g" class="mb-4">FA30G</h6>

            <div class="form-group" v-if="this.showauprill">
              <label for="sample-no"
                >Au, Prill (Mg)<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.auprillmg"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group" v-if="this.showaugrade">
              <label for="sample-no"
                >Au Grade (Gpt)<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.augradegpt"
                style="color: black; background-color: white;"
              />
            </div>
            <h6 class="mb-4" v-if="this.isfa30a">FA30A</h6>
            <div class="form-group" v-if="this.assreadingppm">
              <label for="sample-no"
                >ASS Reading, ppm<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.assreadingppm"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group" v-if="this.showagdore">
              <label for="sample-no"
                >Ag, Dore (Mg)<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.agdoremg"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group" v-if="this.form.transType == 'Carbon'">
              <label for="sample-no"
                >Initial Ag (Gpt)<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.initialaggpt"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group" v-if="this.showcclearance">
              <label for="sample-no"
                >Crusible Clearance<span
                  class="text-danger"
                  aria-required="true"
                >
                  *
                </span></label
              >
              <input
                type=""
                class="form-control input-sm"
                v-model="form.crusibleclearance"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group" v-if="this.form.transType == 'Solids'">
              <label for="sample-no"
                >For Inquart (Mg)<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="number"
                class="form-control input-sm"
                v-model="form.inquartmg"
                style="color: black; background-color: white;"
              />
            </div>
            <div class="form-group">
              <label for="sample-no"
                >Remarks<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type=""
                class="form-control input-sm"
                v-model="form.methodremarks"
                style="color: black; background-color: white;"
              />
            </div>
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
        @click.prevent="saveItem"
      >
        <i data-feather="save" class="mg-r-5"></i> Save
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
      isDeptUser: this.dialogRef.data.isDeptUser,
      isReceiving: this.dialogRef.data.isReceiving,
      errors_exist: false,
      errors: {},
      isdisabled: true,
      isAnalystAssayer: false,
      methodheader: "Method",
      showauprill: false,
      showaugrade: false,
      showassreading: false,
      showagdore: false,
      showcclearance: false,
      isfa30g: false,
      isfa30a: false,
      form: {
        transmittalno: this.dialogRef.data.transmittalno,
        id: this.dialogRef.data.id,
        sampleno: this.dialogRef.data.sampleno,
        samplewtvolume: this.dialogRef.data.samplewtvolume,
        description: this.dialogRef.data.description,
        elements: this.dialogRef.data.elements,
        methodcode: this.dialogRef.data.methodcode,
        comments: this.dialogRef.data.comments,
        source: this.dialogRef.data.source,
        receiving: this.dialogRef.data.isReceiving,
        samplewtgrams: this.dialogRef.data.samplewtgrams,
        fluxg: this.dialogRef.data.fluxg,
        flourg: this.dialogRef.data.flourg,
        niterg: this.dialogRef.data.niterg,
        leadg: this.dialogRef.data.leadg,
        silicang: this.dialogRef.data.silicang,
        crusibleused: this.dialogRef.data.crusibleused,
        isAssayer: this.dialogRef.data.isAssayer,
        isAnalyst: this.dialogRef.data.isAnalyst,
        labbatch: this.dialogRef.data.labbatch,
        transType: this.dialogRef.data.transType,
        auprillmg: this.dialogRef.data.auprillmg,
        augradegpt: this.dialogRef.data.augradegpt,
        assreadingppm: this.dialogRef.data.assreadingppm,
        agdoremg: this.dialogRef.data.agdoremg,
        initialaggpt: this.dialogRef.data.initialaggpt,
        crusibleclearance: this.dialogRef.data.crusibleclearance,
        inquartmg: this.dialogRef.data.inquartmg,
        methodremarks: this.dialogRef.data.methodremarks,
      },
    };
  },
  mounted() {
    if (this.dialogRef.data.sampleno == undefined || this.isDeptUser) {
      this.isdisabled = false;
    }
    if (this.dialogRef.data.isAssayer || this.dialogRef.data.isAnalyst) {
      this.isAnalystAssayer = true;
    }
    if (
      this.dialogRef.data.transType == "Rock" ||
      this.dialogRef.data.transType == "Mine Drill"
    ) {
      this.showauprill = true;
      this.showaugrade = true;
      this.assreadingppm = true;
      this.isfa30g = true;
      this.isfa30a = true;

      this.form.agdoremg = 0;
      this.form.initialaggpt = 0;
      this.form.crusibleclearance = 0;
      this.form.inquartmg = 0;
    } else if (this.dialogRef.data.transType == "Carbon") {
      this.showagdore = true;
      this.showcclearance = true;
      this.methodheader = "Gravimetric Method";

      this.form.auprillmg = 0;
      this.form.augradegpt = 0;
      this.form.assreadingppm = 0;
      this.form.inquartmg = 0;
    } else if (this.dialogRef.data.transType == "Solids") {
      this.showauprill = true;
      this.showaugrade = true;
      this.showagdore = true;
      this.methodheader = "Gravimetric Method";
      this.showcclearance = true;

      this.form.assreadingppm = 0;
      this.form.initialaggpt = 0;
    } else if (this.dialogRef.data.transType == "Cut" || this.dialogRef.data.transType == "Bulk") {
      this.showauprill = true;
      this.showaugrade = true;
      this.showcclearance = true;

      this.form.assreadingppm = 0;
      this.form.agdoremg = 0;
      this.form.initialaggpt = 0;
      this.form.inquartmg = 0;
    } else if (this.dialogRef.data.transType == "Solutions") {
      this.form.auprillmg = 0;
      this.form.augradegpt = 0;
      this.form.assreadingppm = 0;
      this.form.agdoremg = 0;
      this.form.initialaggpt = 0;
      this.form.crusibleclearance = 0;
      this.form.inquartmg = 0;
    }
  },
  methods: {
    closeDialog() {
      this.dialogRef.close();
    },
    async saveItem() {
      var url = "/transItem/store";
      if (this.form.id != undefined) {
        url = "/transItem/update";
      }
      const res = await this.submit("post", url, this.form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.dialogRef.close(this.form);
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
        // this.ermessage(res.data.errors);
      }
    },
  },
};
</script>