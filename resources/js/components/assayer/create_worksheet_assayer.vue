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
          <ol class="breadcrumb breadcrumb-style1 mg-b-10">
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">LIMS</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
              <a :href="dashboard">Assayer</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Create Worksheet
            </li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Create Worksheet - Assayer</h4>
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
    <div class="row">
      <div class="col-lg-6">
        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="date-shift-assayed">Date Shift and Assayed</label>
                <input
                  type="text"
                  class="form-control"
                  id="date-shift-assayed"
                  name="date-shift-assayed"
                  pattern="\d{2}\/\d{2}\/\d{4}"
                  placeholder="mm/dd/yyyy"
                  v-model="form.dateshift"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-shift-assayed">Time</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-shift-assayed"
                  name="time-shift-assayed"
                  v-model="form.timeshift"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-12">
              <div class="form-group">
                <label for="fusion-furnace-no">Fusion Furnace No.</label>
                <input
                  type="text"
                  class="form-control"
                  id="fusion-furnace-no"
                  name="fusion-furnace-no"
                  placeholder="#1"
                  v-model="form.fusionfurno"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-from1">Time From</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-from1"
                  name="time-from1"
                  v-model="form.fusiontimefrom"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-to1">To</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-to1"
                  name="time-to1"
                  v-model="form.fusiontimeto"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="border p-2 p-lg-3 rounded mb-3">
          <div class="row row-sm">
            <div class="col-lg-12">
              <div class="form-group">
                <label for="cupellation-furnace-no"
                  >cupellation Furnace No.</label
                >
                <input
                  type="text"
                  class="form-control"
                  id="cupellation-furnace-no"
                  name="cupellation-furnace-no"
                  placeholder="#1"
                  v-model="form.cupellationfurno"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-from2">Time From</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-from2"
                  name="time-from2"
                  v-model="form.cupellationtimefrom"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label for="time-to2">To</label>
                <input
                  type="time"
                  class="form-control"
                  id="time-to2"
                  name="time-to2"
                  v-model="form.cupellationtimeto"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row row-sm">
          <div class="col-lg-6">
            <div class="form-group">
              <label for="lab-batch">Lab Batch</label>
              <input
                type="text"
                class="form-control"
                id="lab-batch"
                name="lab-batch"
                placeholder="MM-00381"
                v-model="form.labbatch"
                @change="onLabbatchChange"
              />
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label for="type">Type</label>
              <input
                type="text"
                class="form-control"
                id="transType"
                name="transType"
                v-model="form.transType"
                disabled="true"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-group">
          <label for="temperature">Temperature </label>
          <input
            type="text"
            class="form-control"
            id="temperature"
            name="temperature"
            placeholder="Display/Pyrometer"
            v-model="form.temperature"
          />
        </div>

        <div class="form-group">
          <label for="fusion">Fusion</label>
          <input
            type="text"
            class="form-control"
            id="fusion"
            name="fusion"
            placeholder="1050/1098"
            v-model="form.fusion"
          />
        </div>

        <div class="form-group">
          <label for="cupellation">Cupellation</label>
          <input
            type="text"
            class="form-control"
            id="cupellation"
            name="cupellation"
            placeholder="950/993"
            v-model="form.cupellation"
          />
        </div>

        <div class="form-group">
          <label for="mold-used">Mold Used</label>
          <input
            type="text"
            class="form-control"
            id="mold-used"
            name="mold-used"
            placeholder="M1M5"
            v-model="form.moldused"
          />
        </div>

        <div class="form-group">
          <label for="fire-assayer">Fire Assayer</label>
          <select
            class="custom-select tx-base"
            id="fire-assayer"
            name="fire-assayer"
            v-model="form.fireassayer"
          >
            <option v-for="fireassayer in fireassayers" :key="fireassayer.id" :value="fireassayer.name">{{fireassayer.name}}</option>
          </select>
        </div>
      </div>

      <div class="col-lg-12">
        <hr class="mg-t-10 mg-b-30" />
      </div>
    </div>

    <div class="row row-xs">
      <div class="col-lg-12">
        <button
          @click="showDialog"
          class="btn btn-primary tx-13 btn-uppercase mg-b-25"
          data-toggle="modal"
          :disabled="this.disableAdd"
        >
          <i data-feather="plus" class="mg-r-5"></i> Add Insertion
        </button>&nbsp;
        <button
          @click="downloadCSV"
          class="btn btn-success tx-13 btn-uppercase  mg-b-25"
          :disabled="this.disableAdd"
        >
          <i data-feather="download" class="mg-r-5"></i> Download Item Template
        </button>
      </div>
      <div class="col-lg-12">
        <div class="table-responsive-lg">
          <DataTable
            ref="dt"
            :value="items"
            :paginator="true"
            :rows="10"
            stripedRows
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[10, 20, 50]"
            responsiveLayout="scroll"
            :loading="loading"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
            v-model:filters="filters"
            filterDisplay="menu"
            rowIndexVar
            editMode="row"
            dataKey="id"
            v-model:editingRows="editingRows"
            @row-edit-save="onRowEditSave"
          >
            <template #empty> No record found. </template>
            <template #loading> Loading data. Please wait. </template>
            <Column>
              <template #header>
                <div class="custom-control custom-checkbox">
                  <input
                    type="checkbox"
                    @change="checkAll($event)"
                    class="custom-control-input"
                    id="checkboxall"
                  /><label
                    class="custom-control-label"
                    for="checkboxall"
                  ></label>
                </div>
              </template>
              <template #body="slotProps">
                <div class="custom-control custom-checkbox">
                  <input
                    type="checkbox"
                    class="custom-control-input cb"
                    @change="checked(slotProps, $event)"
                    name="chkboxes"
                    style="opacity: 1"
                    :id="slotProps.data.id"
                  />
                  <label
                    class="custom-control-label"
                    :for="slotProps.data.id"
                  ></label>
                </div>
              </template>
            </Column>
            <Column header="Sample No.">
              <template #body="slotProps">
                {{ slotProps.index + 1 }}
              </template></Column
            >
            <Column field="id" hidden="true"></Column>
            <Column field="sampleno" header="Sample Code" :sortable="true"></Column>
            <Column
              field="source"
              header="Source"
              :sortable="true"
              style="min-width: 8rem"
            ></Column>
            <Column field="samplewtgrams" header="Sample Wt.(Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="crusibleused" header="Crusible Used" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="transmittalno" header="Transmittal No." :sortable="true"></Column>
            <Column field="fluxg" header="Flux (Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="flourg" header="Flour (Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="niterg" header="Niter (Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="leadg" header="Lead (Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column field="silicang" header="Silican (Grams)" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="number"
                  min="0"
                  autofocus
                />
              </template>
            </Column>
            <Column
              :rowEditor="true"
              style="width: 7%; min-width: 8rem"
              bodyStyle="text-align:right"
            >
            </Column>
            <Column
              :exportable="false"
              style="min-width: 8rem"
              header="Actions"
            >
              <template #body="slotProps">
                <!--<Button
                  v-bind:title="editMsg"
                  icon="pi pi-pencil"
                  class="p-button-rounded p-button-success mr-2"
                  @click="editItem(slotProps)"
                  :id="'btn' + slotProps.data.id"
                  name="btnedit"
                  disabled
                /> -->
                <Button
                  v-bind:title="dupMsg"
                  icon="pi pi-clone"
                  class="p-button-rounded p-button-warning mr-2"
                  @click="duplicateSample(slotProps)"
                  :id="'btndup' + slotProps.data.id"
                  name="btndup"
                  disabled
                />
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
    <!-- row -->

    <hr class="mg-t-30 mg-b-30" />

    <div class="row">
      <div class="col-lg-12">
        <div class="d-lg-flex justify-content-lg-end">
          <div
            class="
              form-group
              d-flex
              flex-column flex-lg-row
              align-items-lg-center
            "
          >
            <input type="hidden" v-model="form.itemFile" />
            <label for="customFile" class="mg-r-10">Attached CSV</label>
            <div class="custom-file mb-0 mb-lg-2">
              <input
                type="file"
                class="custom-file-input"
                id="customFile"
                ref="file"
                name="attached-csv"
                v-on:change="onFileChange"
                :disabled="disableAdd"
                accept=".csv"
              />
              <label
                class="custom-file-label"
                for="customFile"
                data-button-label="Browse"
                >{{ fileLabel }}</label
              >
            </div>
            <button
              type="submit"
              class="
                btn btn-primary
                tx-13
                btn-uppercase
                mr-2
                mb-2
                ml-lg-1
                mr-lg-0
              "
              :disabled="disableAdd"
              @click.prevent="uploadItem"
            >
              <i data-feather="upload" class="mg-r-5"></i> Upload
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row flex-column-reverse flex-lg-row">
      <div class="col-lg-6">
        <a :href="dashboard" class="btn btn-white tx-13 btn-uppercase"
          ><i data-feather="arrow-left" class="mg-r-5"></i> Back to Dashboard</a
        >
      </div>
      <div class="col-lg-6 d-flex justify-content-start justify-content-lg-end">
        <!-- <a
          :href="dashboard"
          class="btn btn-white tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          ><i data-feather="x-circle" class="mg-r-5"></i> Cancel</a
        > -->
        <button
          type="submit"
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="saveWorksheet"
        >
          <i data-feather="save" class="mg-r-5"></i> Save
        </button>
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
  <DynamicDialog />
  <ConfirmDialog></ConfirmDialog>
</template>
<script>
import item from "../../components/item/item";
import { h } from "vue";
import Button from "primevue/button";
export default {
  props: ["transids", "transmittal"],
  data() {
    return {
      dashboard: this.$env_Url + "/assayer/dashboard",
      loading: true,
      fileLabel: "Choose File",
      editingRows: [],
      cocPath: "",
      editMsg: "Update Sample",
      dupMsg: "Duplicate Sample",
      items: [],
      errors_exist: false,
      errors: {},
      disableAdd: true,
      disableEdit: true,
      labbatchExists: false,
      selectedItemsId: [],
      fireassayers : [],
      form: {
        labbatch: "",
        dateshift: "",
        timeshift: "",
        fusionfurno: "",
        fusiontimefrom: "",
        fusiontimeto: "",
        fusion: "",
        cupellationfurno: "",
        cupellationtimefrom: "",
        cupellationtimeto: "",
        cupellation: "",
        temperature: "Display/Pyrometer",
        moldused: "",
        fireassayer: "",
        ids: this.transids,
        transType: this.transmittal.transType,
        itemIds: "",
        itemFile: null
      },
    };
  },
  created() {
    this.fetchItems();
    this.fetchFireAssayer();
    this.loading = false;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    var hour = String(today.getHours()).padStart(2, "0");
    var hourto = String(today.getHours() + 1).padStart(2, "0");
    var minutes = String(today.getMinutes()).padStart(2, "0");
    var time = hour + ":" + minutes;
    var timeto = hourto + ":" + minutes;
    today = mm + "/" + dd + "/" + yyyy;

    this.form.dateshift = today;
    this.form.timeshift = time;
    this.form.fusiontimefrom = time;
    this.form.fusiontimeto = timeto;
    this.form.cupellationtimefrom = time;
    this.form.cupellationtimeto = timeto;
  },
  updated() {
    this.disableAdd = true;
    var labbatch = this.form.labbatch;
    if (labbatch == null) {
      labbatch = "";
    }
    if (labbatch != "") {
      this.disableAdd = false;
    }
  },
  methods: {
    onLabbatchChange() {
      this.checkBatchNo();
    },
    async checkBatchNo() {
      this.errors_exist = false;
      this.errors = {};
      const res = await this.callApiwParam(
        "post",
        "/assayer/checkBatchNo",
        this.form
      );

      if (res.data.length > 0) {
        var status = "Active";
        if (res.data[0]["isdeleted"] == 1) {
          status = "Deleted";
        }
        this.labbatchExists = true;
        if (!this.labbatchExists) {
          this.fetchItems();
        }
        this.errors = {
          error: [
            "Lab Batch No already exists! : " +
              this.form.labbatch +
              " | Status : " +
              status,
          ],
        };
        this.errors_exist = true;
        this.form.labbatch = "";
      }
    },
    async fetchItems() {
      const res = await this.callApiwParam(
        "post",
        "/assayer/getItems",
        this.form
      );
      this.items = res.data;
    },
    async fetchFireAssayer() {
      const res = await this.getDataFromDB("get", "/fireassayers/getFireAssayerActive");

      this.fireassayers = res.data;
    },
    onFileChange(e) {
      this.form.itemFile = this.$refs.file.files[0];
      this.fileLabel = this.form.itemFile.name;
    },
    async uploadItem() {
      //this.fileLabel = this.form.transmittalno + "_" + this.fileLabel;
      let form = new FormData();
      form.append("itemFile", this.form.itemFile);
      _.each(this.form, (value, key) => {
        form.append(key, value);
      });
      const res = await this.submit("post", "/assayer/uploaditems", form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.smessage();
        this.fetchItems();
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
        // this.ermessage(res.data.errors);
      }
    },
    async onRowEditSave(event) {
      let { newData, index } = event;
      newData.samplewtgrams = parseInt(newData.samplewtgrams)
      newData.fluxg = parseInt(newData.fluxg)
      newData.flourg = parseInt(newData.flourg)
      newData.niterg = parseInt(newData.niterg)
      newData.leadg = parseInt(newData.leadg)
      newData.silicang = parseInt(newData.silicang)
      this.items[index] = newData;

      let itemForm = {
        id: newData.id,
        sampleno: newData.sampleno,
        samplewtgrams: newData.samplewtgrams,
        fluxg: newData.fluxg,
        flourg: newData.flourg,
        niterg: newData.niterg,
        leadg: newData.leadg,
        silicang: newData.silicang,
        crusibleused: newData.crusibleused,
        isAssayer: true,
      }
      const res = await this.submit("post", "/transItem/update", itemForm, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        this.smessage();
        this.fetchItems();
      } else {
        this.errors_exist = true;
        this.errors = res.data.errors;
        // this.ermessage(res.data.errors);
      }
      
    },
    editItem(data) {
      this.showDialog(data.data);
    },
    showDialog(data) {
      const dialogRef = this.$dialog.open(item, {
        props: {
          header: "Transmittal item",
          style: {
            width: "50vw",
          },
          breakpoints: {
            "960px": "75vw",
            "640px": "90vw",
          },
          modal: true,
        },
        data: {
          transmittalno: data.transmittalno,
          id: data.id,
          sampleno: data.sampleno,
          samplewtvolume: data.samplewtvolume,
          description: data.description,
          elements: data.elements,
          methodcode: data.methodcode,
          comments: data.comments,
          isDeptUser: false,
          isReceiving: false,
          isAssayer: true,
          source: data.source,
          samplewtgrams: data.samplewtgrams,
          fluxg: data.fluxg,
          flourg: data.flourg,
          niterg: data.niterg,
          leadg: data.leadg,
          silicang: data.silicang,
          crusibleused: data.crusibleused,
          labbatch: this.form.labbatch,
        },
        onClose: (options) => {
          this.fetchItems();
        },
      });
    },
    async saveWorksheet() {
      if (this.selectedItemsId.length > 0) {
        var ids = "";
        this.selectedItemsId.forEach(function (element, index) {
          if (index == 0) {
            ids = element.id;
          } else {
            ids = ids + "," + element.id;
          }
        });
        this.form.itemIds = ids;
        const res = await this.submit("post", "/assayer/store", this.form, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        });
        if (res.status === 200) {
          window.location.href = this.$env_Url + "/assayer/dashboard";
        } else {
          this.errors_exist = true;
          this.errors = res.data.errors;
        }
      } else {
        this.errors = {
          error: ["Select atleast 1 sample!"],
        };
        this.errors_exist = true;
      }
    },
    checked(sample, event) {
      sample.data.rowEditor = !sample.data.isChecked;
      if (event.target.checked) {
        this.selectedItemsId.push(sample.data);
        //document.getElementById("btn" + sample.data.id).disabled = false;
        document.getElementById("btndup" + sample.data.id).disabled = false;
      } else {
        //document.getElementById("btn" + sample.data.id).disabled = true;
        document.getElementById("btndup" + sample.data.id).disabled = true;
        _.remove(this.selectedItemsId, function (val) {
          return val === sample.data;
        });
      }
    },
    downloadCSV() {
      axios.post(this.$env_Url+'/assayer/download-csv', this.form, { responseType: 'blob' })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'Labbatch_'+this.form.labbatch+'_.csv');
          document.body.appendChild(link);
          link.click();
        })
        .catch(error => {
          alert("Error: "+error)
        });
    },
    checkAll(event) {
      var checkboxes = document.getElementsByName("chkboxes");
      var btnEdits = document.getElementsByName("btnedit");
      var btnDups = document.getElementsByName("btndup");
      for (var chkbox of checkboxes) {
        chkbox.checked = event.target.checked;
      }
      for (var btnEdit of btnEdits) {
        btnEdit.disabled = !event.target.checked;
      }
      for (var btnDup of btnDups) {
        btnDup.disabled = !event.target.checked;
      }
      this.selectedItemsId = [];
      if (event.target.checked) {
        for (var item of this.items) {
          this.selectedItemsId.push(item);
        }
      }
    },
    duplicateSample(data) {
      this.$confirm.require({
        message: "Do you want to duplicate sample "  + data.data.sampleno + "?",
        header: "Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-info",
        accept: async () => {
          const res = await this.submit("post", "/assayer/duplicateSample", {
            id: data.data.id,
          });
          if (res.status === 200) {
            this.$toast.add({
                severity: "warn",
                summary: "Confirmed",
                detail: "Sample Code " + data.data.sampleno + " Duplicated!",
                life: 3000,
            });
            this.fetchItems();
          } else {
            this.ermessage();
          }
        },
      });
    },
  },
};
</script>
