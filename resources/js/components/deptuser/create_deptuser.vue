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
              <a :href="dashboard">Dept. Requesters</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
          </ol>
        </nav>
        <h4 class="mg-b-0 tx-spacing--1">Create Transmittal - Dept.User</h4>
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
            <label for="customFile" class="mg-r-10"
              >Attached COC</label
            >
            <div class="custom-file mb-0 mb-lg-2">
              <input
                type="file"
                class="custom-file-input"
                id="customFile"
                ref="cocFile"
                name="attached-csv"
                @change="onCoCFileChange"
              />
              <label
                class="custom-file-label"
                for="customFile"
                data-button-label="Upload"
                >{{ cocFileLabel }}</label
              >
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-group">
          <label for="transmittal-no"
            >Transmittal No.<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <input
            type="text"
            class="form-control"
            id="transmittal-no"
            name="transmittal-no"
            v-model="form.transmittalno"
            @change="onTransmittalNoChange"
          />
        </div>

        <div class="row row-sm">
          <div class="col-lg-6">
            <div class="form-group">
              <label for="date-submitted"
                >Date Submitted<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="text"
                class="form-control"
                id="date-submitted"
                name="date-submitted"
                pattern="\d{2}\/\d{2}\/\d{4}"
                placeholder="mm/dd/yyyy"
                disabled="true"
                v-model="form.datesubmitted"
              />
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group">
              <label for="time-submitted"
                >Time Submitted<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="time"
                class="form-control"
                id="time-submitted"
                name="time-submitted"
                v-model="form.timesubmitted"
                disabled="true"
              />
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="email-address"
            >Email Address<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <select
            class="custom-select tx-base"
            id="type"
            name="type"
            v-model="form.email_address"
          >
            <option v-for="officer in officers" :key="officer.id" :value="officer.email">{{officer.email}}</option>
          </select>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="row row-sm">
          <div class="col-lg-8">
            <div class="form-group">
              <label for="purpose"
                >Purpose<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="text"
                class="form-control"
                id="purpose"
                name="purpose"
                v-model="form.purpose"
              />
            </div>
          </div>
          <div class="col-lg-4">
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
                v-model="form.transType"
              >
                <option value="Rock">Rock</option>
                <option value="Carbon">Carbon</option>
                <option value="Bulk">Bulk</option>
                <option value="Cut">Cut</option>
                <option value="Mine Drill">Mine Drill</option>
                <option value="Solids">Solids</option>
                <option value="Solutions">Solutions</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row row-sm">
          <div class="col-lg-4">
            <div class="form-group">
              <label for="date-needed"
                >Date Needed<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <input
                type="text"
                class="form-control"
                id="date-needed"
                name="date-needed"
                pattern="\d{2}\/\d{2}\/\d{4}"
                placeholder="mm/dd/yyyy"
              />
            </div>
          </div>

          <div class="col-lg-4">
            <div class="form-group">
              <label for="priority"
                >Priority<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <select
                class="custom-select tx-base"
                id="priority"
                v-model="form.priority"
                name="priority"
              >
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
              </select>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="form-group">
              <label for="status"
                >Status<span class="text-danger" aria-required="true">
                  *
                </span></label
              >
              <select
                class="custom-select tx-base"
                id="status"
                name="status"
                v-model="form.status"
                disabled="true"
              >
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="source"
            >Source<span class="text-danger" aria-required="true">
              *
            </span></label
          >
          <input
            type="text"
            class="form-control"
            id="source"
            name="source"
            v-model="form.source"
          />
        </div>
      </div>
      <div class="col-lg-12">
        <hr class="mg-t-10 mg-b-30" />
      </div>
    </div>
    <div class="row row-xs">
      <div class="col-lg-6 d-flex justify-content-start">
        <!--
        <button
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click="showDialog"
          :disabled="disableUpload"
        >
          <i data-feather="plus" class="mg-r-5"></i> Add Item
        </button>
        -->
        <a
          :href="this.templatePath + '/template/Item Template.csv'"
          class="btn btn-success tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
        >
          <i data-feather="download" class="mg-r-5"></i> Download Item Template
        </a>
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

            <Column header="Item No.">
              <template #body="slotProps">
                {{ slotProps.index + 1 }}
              </template></Column
            >
            <Column field="id" hidden="true"></Column>
            <Column field="sampleno" header="Sample No." :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                  autofocus
                />
              </template>
            </Column>
            <Column field="description" header="Description" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                />
              </template>
            </Column>
            <Column field="elements" header="Elements" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                />
              </template>
            </Column>
            <Column field="methodcode" header="Method Code" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
                />
              </template>
            </Column>
            <Column field="comments" header="Comments" :sortable="true">
              <template #editor="{ data, field }">
                <InputText
                  v-model="data[field]"
                  type="text"
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
                  v-bind:title="edititem"
                  icon="pi pi-pencil"
                  class="p-button-rounded p-button-success mr-2"
                  @click="editItem(slotProps)"
                />-->
                <Button
                  v-bind:title="deleteitem"
                  icon="pi pi-trash"
                  class="p-button-rounded p-button-warning mr-2"
                  @click="deleteItem(slotProps)"
                  :disabled="!disableClick"
                />
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
    <!-- row -->
    <div class="col-lg-12" style="margin-top: 10px">
      <div class="row row-sm">
        <div class="col-lg-2">
          <div class="form-group">
            <input type="text" class="form-control" id="add_sampleno" name="add_sampleno" placeholder="Sample No."
            />
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input type="text" class="form-control" id="add_description" name="add_description" placeholder="Description"
            />
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input type="text" class="form-control" id="add_elements" name="add_elements" placeholder="Elements"
            />
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input type="text" class="form-control" id="add_methodcode" name="add_methodcode" placeholder="Method Code"
            />
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input type="text" class="form-control" id="add_comments" name="add_comments" placeholder="Comments"
            />
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <button
              class="
                btn btn-primary
                tx-13
                btn-uppercase
                mr-2
                mb-2
                ml-lg-1
                mr-lg-0
              "
              @click="addItem"
              :disabled="disableUpload"
            >
              <i data-feather="plus" class="mg-r-5"></i> Add Item
            </button>
          </div>
        </div>
      </div>
    </div>
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
                :disabled="disableUpload"
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
              :disabled="disableUpload"
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
        <button
          type="reset"
          class="btn btn-white tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="reset"
        >
          <i data-feather="x-circle" class="mg-r-5"></i> Cancel
        </button>
        <button
          type="submit"
          class="btn btn-primary tx-13 btn-uppercase mr-2 mb-2 ml-lg-1 mr-lg-0"
          @click.prevent="saveTransmittal"
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
  data() {
    return {
      disableClick: true,
      dashboard: this.$env_Url + "/deptuser/dashboard",
      loading: true,
      disableUpload: true,
      editingRows: [],
      items: [],
      itemFile: null,
      COCitemFile: null,
      fileLabel: "Choose File",
      cocFileLabel: "Choose File",
      templatePath: this.$env_Url,
      errors_exist: false,
      seconds: 0,
      errors: {},
      transNoExists: false,
      timed: '',
      officers: [],
      form: {
        id: 0,
        transmittalno: "",
        purpose: "",
        datesubmitted: "",
        timesubmitted: "",
        date_needed: "",
        priority: "Low",
        status: "Pending",
        email_address: "",
        source: "",
        itemID: "",
        transType: "Rock",
      },
    };
  },
  created() {
    // this.fetchItems();
    this.fetchDeptOfficerEmails();
    this.loading = false;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();
    var hour = String(today.getHours()).padStart(2, "0");
    var minutes = String(today.getMinutes()).padStart(2, "0");
    var time = hour + ":" + minutes;
    today = mm + "/" + dd + "/" + yyyy;

    this.timed = time;
    this.form.datesubmitted = today;
    //this.form.date_needed = today
    this.form.timesubmitted = time;
  },
  mounted() {
    this.tempInsert();
    this.tempChangePrio();
  },
  updated() {
    this.disableUpload = true;
    var transno = this.form.transmittalno;
    if (transno == null) {
      transno = "";
    }
    if (transno != "") {
      this.disableUpload = false;
    }
  },
  methods: {
    onFileChange(e) {
      this.itemFile = this.$refs.file.files[0];
      this.fileLabel = this.itemFile.name;
    },
    onCoCFileChange(e) {
      this.COCitemFile = this.$refs.cocFile.files[0];
      this.cocFileLabel = this.COCitemFile.name;
    },
    onTransmittalNoChange() {
      if (this.form.transmittalno != "") {
        this.checkTransno();
       
      }
    },
    async checkTransno() {
      this.errors_exist = false;
      this.errors = {};
      const res = await this.callApiwParam(
        "post",
        "/deptuser/checkTransNo",
        this.form
      );
      if (res.data.length > 0) {
        var status = "Active";
        if (res.data[0]["isdeleted"] == 1) {
          status = "Deleted";
        }
        this.transNoExists = true;
        this.errors = {
          error: [
            "Transmittal No already exists! : " +
              this.form.transmittalno +
              " | Status : " +
              status,
          ],
        };
        this.errors_exist = true;
        this.form.transmittalno = "";
      }
      
      if (!this.transNoExists) {
          this.fetchItems();
        }
    },
    async fetchItems() {
      const res = await this.callApiwParam(
        "post",
        "/transItem/getItems",
        this.form
      );
      this.items = res.data;
    },

    async fetchDeptOfficerEmails(){
      const res = await this.getDataFromDB("get", "/deptuser/getDeptOfficerEmails");
      this.officers = res.data;
    },

    async uploadItem() {
      this.fileLabel = this.form.transmittalno + "_" + this.fileLabel;
      let form = new FormData();
      form.append("itemFile", this.itemFile);
      _.each(this.form, (value, key) => {
        form.append(key, value);
      });
      const res = await this.submit("post", "/transItem/uploaditems", form, {
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
    async saveTransmittal() {
      this.disableClick = false;
      this.form.date_needed = document.getElementById("date-needed").value;
      this.form.datesubmitted = document.getElementById("date-submitted").value;
      this.cocFileLabel = this.form.transmittalno + "_" + this.cocFileLabel;
      let form = new FormData();
      form.append("cocFile", this.COCitemFile);
      _.each(this.form, (value, key) => {
        form.append(key, value);
      });
      const res = await this.submit("post", "/deptuser/store", form, {
        headers: {
          "Content-Type":
            "multipart/form-data; charset=utf-8; boundary=" +
            Math.random().toString().substr(2),
        },
      });
      if (res.status === 200) {
        window.location.href = this.$env_Url + "/deptuser/dashboard";
      } else {
        this.disableClick = true;
        this.errors_exist = true;
        this.errors = res.data.errors;
        // this.ermessage(res.data.errors);
      }
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
          transmittalno: this.form.transmittalno,
          id: data.id,
          sampleno: data.sampleno,
          description: data.description,
          elements: data.elements,
          methodcode: data.methodcode,
          comments: data.comments,
          isDeptUser: true,
          isReceiving: false,
          source: this.form.source,
        },
        onClose: (options) => {
          this.fetchItems();
        },
      });
    },
    reset() {
      this.form.transmittalno = null;
      this.form.purpose = "";
      this.form.datesubmitted = "";
      this.form.timesubmitted = "";
      this.form.date_needed = "";
      this.form.priority = "Low";
      this.form.status = "Pending";
      this.form.email_address = "";
      this.form.source = "";
    },
    deleteItem(data) {
      let src = data.data.id,
        alt = data.data.id;
      this.form.itemID = alt;

      this.$confirm.require({
        message: "Do you want to delete this item?",
        header: "Delete Confirmation",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        accept: async () => {
          const res = await this.deleteRecord("post", "/transItem/delete", {
            id: data.data.id,
          });
          if (res.status === 200) {
            this.rmessage();
            this.fetchItems();
          } else {
            this.ermessage();
          }
        },
      });
    },
    async addItem(){
      var error = "";
      if (document.getElementById("add_sampleno").value == "") {
        error = "Required Field: Sample No not found!";
        this.singleermessage(error);
      } else if (document.getElementById("add_description").value == "") {
        error = "Required Field: Description is required!";
        this.singleermessage(error);
      } else if (document.getElementById("add_elements").value == "") {
        error = "Required Field: Elements is required!";
        this.singleermessage(error);
      } else if (document.getElementById("add_methodcode").value == "") {
        error = "Required Field: Method Code is required!";
        this.singleermessage(error);
      }

      if (error == "") {
        const obj = {
          sampleno: document.getElementById("add_sampleno").value,
          description: document.getElementById("add_description").value,
          elements: document.getElementById("add_elements").value,
          methodcode: document.getElementById("add_methodcode").value,
          comments: document.getElementById("add_comments").value,
          transmittalno: this.form.transmittalno,
          source: this.form.source,
        }
        const res = await this.submit("post", "/transItem/store", obj, {
          headers: {
            "Content-Type":
              "multipart/form-data; charset=utf-8; boundary=" +
              Math.random().toString().substr(2),
          },
        });
        if (res.status === 200) {
          this.fetchItems();
          document.getElementById("add_sampleno").value = "";
          document.getElementById("add_description").value = "";
          document.getElementById("add_elements").value = "";
          document.getElementById("add_methodcode").value = "";
          document.getElementById("add_comments").value = "";
        } else {
          this.errors_exist = true;
          this.errors = res.data.errors;
          // this.ermessage(res.data.errors);
        }
      }
    },
    async onRowEditSave(event) {
      let { newData, index } = event;
      this.items[index] = newData;

      let itemForm = {
        id: newData.id,
        sampleno: newData.sampleno,
        description: newData.description,
        elements: newData.elements,
        comments: newData.comments,
        methodcode: newData.methodcode,
        transmittalno: this.form.transmittalno,
        source: this.form.source
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
    async autosave() {
      this.seconds = this.seconds + 1;
      if (this.seconds == 60) {
        if (this.form.transmittalno != "") {
          if (this.form.purpose == null) {
            this.form.purpose = "";
          }
          if (this.form.email_address == null) {
            this.form.email_address = "";
          }
          if (this.form.source == null) {
            this.form.source = "";
          }
          this.form.date_needed = document.getElementById("date-needed").value;
          this.form.datesubmitted =
            document.getElementById("date-submitted").value;
          let form = new FormData();
          form.append("cocFile", this.COCitemFile);
          _.each(this.form, (value, key) => {
            form.append(key, value);
          });
          const res = await this.submit("post", "/deptuser/autosave", form, {
            headers: {
              "Content-Type":
                "multipart/form-data; charset=utf-8; boundary=" +
                Math.random().toString().substr(2),
            },
          });
          if (res.status === 200) {
            this.form.id = res.data.id;
          }
        }
        this.seconds = 0;
      }
    },

    async autoSetPrio(){
      const dateA = new Date(this.form.datesubmitted+' '+this.timed);
      const dateB = new Date(document.getElementById("date-needed").value+' '+this.timed);
      const timeDiff = dateB - dateA;
      const hoursDiff = timeDiff / (1000 * 60 * 60);
      const roundedDiff = hoursDiff.toFixed(2);
      if(parseInt(roundedDiff) <= 24){
        this.form.priority = 'High'
      }else if(parseInt(roundedDiff) > 24 && parseInt(roundedDiff) <= 48){
        this.form.priority = 'Medium'
      }else{
        this.form.priority = 'Low'
      }
    }, 

    tempInsert: function () {
      setInterval(() => {
        this.autosave();
      }, 1000);
    },

    tempChangePrio: function(){
      setInterval(() => {
        this.autoSetPrio()
      }, 100);
    }
  },
};
</script>
